<?php

declare(strict_types=1);

namespace Pollen\View\Extensions;

use Pollen\Support\Proxy\FakerProxy;
use Pollen\View\Engines\Plates\PlatesViewEngine;
use Pollen\View\Engines\Twig\TwigViewEngine;
use Pollen\View\ViewEngineInterface;
use Pollen\View\ViewExtension;
use Pollen\ViewBlade\BladeViewEngine;
use Twig\TwigFunction;

class FakerViewExtension extends ViewExtension
{
    use FakerProxy;

    protected ?string $locale = null;

    /**
     * @inheritDoc
     */
    public function register(ViewEngineInterface $viewEngine)
    {
        if ($viewEngine instanceof PlatesViewEngine) {
            $viewEngine->platesEngine()->registerFunction(
                $this->getName(),
                function (...$args) {
                    return $this->getFormatter(...$args);
                }
            );
        }

        if ($viewEngine instanceof TwigViewEngine) {
            $viewEngine->twigEnvironment()->addFunction(
                new TwigFunction(
                    $this->getName(),
                    function (...$args) {
                        return $this->getFormatter(...$args);
                    },
                    [
                        'is_safe' => ['html'],
                    ]
                )
            );
        }

        if ($viewEngine instanceof BladeViewEngine) {
            $viewEngine->blade()->directive(
                $this->getName(),
                function ($expression) {
                    /** @var array $args */
                    $args = array_map(
                        function ($arg) {
                            eval('$param=' . trim($arg) . ';');

                            /** @var array $param */
                            return $param;
                        },
                        explode(',', $expression)
                    );

                    return $this->getFormatter(...$args);
                }
            );
        }

        return null;
    }

    /**
     * Get Faker formatter.
     * @see https://fakerphp.github.io/formatters/
     *
     * @param string $formatter
     * @param ...$args
     *
     * @return string
     */
    protected function getFormatter(string $formatter, ...$args): string
    {
        return $this->faker()->$formatter(...$args);
    }
}