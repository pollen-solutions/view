<?php

declare(strict_types=1);

namespace Pollen\View;

use Pollen\Support\Proxy\ContainerProxyInterface;

interface ViewExtensionInterface extends ContainerProxyInterface
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Register extension in view engine.
     *
     * @param ViewEngineInterface $viewEngine
     *
     * @return mixed
     */
    public function register(ViewEngineInterface $viewEngine);

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return ViewExtensionInterface
     */
    public function setName(string $name): ViewExtensionInterface;
}