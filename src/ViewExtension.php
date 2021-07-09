<?php

declare(strict_types=1);

namespace Pollen\View;

use Pollen\Support\Proxy\ContainerProxy;
use Psr\Container\ContainerInterface as Container;

abstract class ViewExtension implements ViewExtensionInterface
{
    use ContainerProxy;

    protected string $name;

    /**
     * @param string|null $name
     * @param Container|null $container
     */
    public function __construct(?string $name = null, ?Container $container = null)
    {
        if ($name !== null) {
            $this->name = $name;
        }

        if ($container !== null) {
            $this->setContainer($container);
        }
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    abstract public function register(ViewEngineInterface $viewEngine);

    /**
     * @inheritDoc
     */
    public function setName(string $name): ViewExtensionInterface
    {
        $this->name = $name;

        return $this;
    }
}