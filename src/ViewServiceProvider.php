<?php

declare(strict_types=1);

namespace Pollen\View;

use Pollen\Container\BootableServiceProvider;
use Pollen\Event\EventDispatcherInterface;
use Pollen\Kernel\Events\ConfigLoadEvent;
use Pollen\View\Engines\Plates\PlatesViewEngine;
use Pollen\View\Engines\Twig\TwigViewEngine;
use Pollen\View\Extensions\FakerViewExtension;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ViewServiceProvider extends BootableServiceProvider
{
    protected $provides = [
        ViewInterface::class,
        ViewManagerInterface::class,
        PlatesViewEngine::class,
        TwigViewEngine::class,
        FakerViewExtension::class
    ];

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        try {
            /** @var EventDispatcherInterface $event */
            if ($event = $this->getContainer()->get(EventDispatcherInterface::class)) {
                $event->subscribeTo('config.load', static function (ConfigLoadEvent $event) {
                    if (is_callable($config = $event->getConfig('view'))) {
                        $config($event->getApp()->get(ViewManagerInterface::class), $event->getApp());
                    }
                });
            }
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            unset($e);
        }
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(ViewManagerInterface::class, function () {
            return new ViewManager($this->getContainer());
        });

        $this->getContainer()->share(ViewInterface::class, function () {
            /** @var ViewManagerInterface $viewManager */
            $viewManager = $this->getContainer()->get(ViewManagerInterface::class);

            return $viewManager->getDefaultView();
        });

        $this->getContainer()->share(PlatesViewEngine::class, function () {
            return new PlatesViewEngine();
        });

        $this->getContainer()->share(TwigViewEngine::class, function () {
            return new TwigViewEngine();
        });

        $this->getContainer()->share(FakerViewExtension::class, function () {
            return new FakerViewExtension();
        });
    }
}
