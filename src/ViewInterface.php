<?php

declare(strict_types=1);

namespace Pollen\View;

interface ViewInterface
{
    /**
     * Add a template extension.
     *
     * @param string $name
     * @param ViewExtensionInterface|callable|string|null $extension
     *
     * @return static
     */
    public function addExtension(string $name, $extension = null): ViewInterface;

    /**
     * Get instance of view engine.
     *
     * @return ViewEngineInterface
     */
    public function getEngine(): ViewEngineInterface;

    /**
     * Return a view template.
     *
     * @param string $name
     * @param array $datas
     *
     * @return string
     */
    public function render(string $name, array $datas = []) : string;

    /**
     * Set template cache directory.
     *
     * @param string|null $cacheDir Absolute path of templates cache directory.
     *
     * @return static
     */
    public function setCacheDir(?string $cacheDir = null): ViewInterface;

    /**
     * Set template directory.
     *
     * @param string $directory Absolute path of templates directory.
     *
     * @return static
     */
    public function setDirectory(string $directory): ViewInterface;

    /**
     * Set resolved file extension.
     *
     * @param string $fileExtension
     *
     * @return static
     */
    public function setFileExtension(string $fileExtension): ViewInterface;

    /**
     * Set template override directory.
     *
     * @param string $overrideDir Absolute path of override templates directory.
     *
     * @return static
     */
    public function setOverrideDir(string $overrideDir): ViewInterface;

    /**
     * Add a piece of shared data to all templates.
     *
     * @param string|array $key
     * @param mixed $value
     *
     * @return static
     */
    public function share($key, $value = null): ViewInterface;
}
