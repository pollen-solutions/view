<?php

declare(strict_types=1);

namespace Pollen\View;

use Pollen\Support\Proxy\ContainerProxyInterface;
use Pollen\Support\Proxy\ViewProxyInterface;

interface ViewEngineInterface extends ContainerProxyInterface, ViewProxyInterface
{
    /**
     * Add a template extension.
     *
     * @param string $name
     * @param ViewExtensionInterface|callable|string|null $extension
     *
     * @return static
     */
    public function addExtension(string $name, $extension): ViewEngineInterface;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function exists(string $name): bool;

    /**
     * Return a view template.
     *
     * @param string $name
     * @param array $datas
     *
     * @return string
     */
    public function render(string $name, array $datas = []): string;

    /**
     * Set template cache directory.
     *
     * @param string|null $cacheDir Absolute path of templates cache directory.
     *
     * @return static
     */
    public function setCacheDir(?string $cacheDir = null): ViewEngineInterface;

    /**
     * Set template directory.
     *
     * @param string $directory Absolute path of templates directory.
     *
     * @return static
     */
    public function setDirectory(string $directory): ViewEngineInterface;

    /**
     * Set resolved file extension.
     *
     * @param string $fileExtension
     *
     * @return static
     */
    public function setFileExtension(string $fileExtension): ViewEngineInterface;

    /**
     * Set view engine options.
     *
     * @param array $options
     *
     * @return static
     */
    public function setOptions(array $options = []): ViewEngineInterface;

    /**
     * Set template override directory.
     *
     * @param string $overrideDir Absolute path of override templates directory.
     *
     * @return static
     */
    public function setOverrideDir(string $overrideDir): ViewEngineInterface;

    /**
     * Add a piece of shared data to all templates.
     *
     * @param string|array $key
     * @param mixed $value
     *
     * @return static
     */
    public function share($key, $value = null): ViewEngineInterface;
}