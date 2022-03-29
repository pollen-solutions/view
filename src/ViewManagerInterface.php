<?php

declare(strict_types=1);

namespace Pollen\View;

use Closure;
use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Proxy\ContainerProxyInterface;

interface ViewManagerInterface extends
    BootableTraitInterface,
    ContainerProxyInterface,
    ViewInterface
{
    /**
     * Booting.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Create a new view.
     *
     * @param string|ViewEngineInterface|null $viewEngineDef
     * @param Closure|null $engineCallback
     * @param bool $withShareExtensions
     *
     * @return ViewInterface
     */
    public function createView($viewEngineDef = null, ?Closure $engineCallback = null, bool $withShareExtensions = true): ViewInterface;

    /**
     * Get default view.
     *
     * @return ViewInterface
     */
    public function getDefaultView(): ViewInterface;

    /**
     * Get view extension instance.
     *
     * @param string $name
     *
     * @return ViewExtensionInterface|callable|null
     */
    public function getExtension(string $name);

    /**
     * View engine registration.
     *
     * @param string $name
     * @param ViewEngineInterface|string $engineDef
     * @param bool $asDefault
     *
     * @return static
     */
    public function registerEngine(string $name, $engineDef, bool $asDefault = false): ViewManagerInterface;

    /**
     * View Extension registration.
     *
     * @param string $name
     * @param ViewExtensionInterface|string|callable $extensionDef
     * @param bool $shared
     *
     * @return static
     */
    public function registerExtension(string $name, $extensionDef, bool $shared = false): ViewManagerInterface;

    /**
     * Set default view engine.
     *
     * @param string $name
     *
     * @return static
     */
    public function setDefaultEngine(string $name): ViewManagerInterface;
}
