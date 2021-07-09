<?php

declare(strict_types=1);

namespace Pollen\View;

class View implements ViewInterface
{
    protected ViewEngineInterface $viewEngine;

    /**
     * @param ViewEngineInterface $viewEngine
     */
    public function __construct(ViewEngineInterface $viewEngine)
    {
        $this->viewEngine = $viewEngine;
    }

    /**
     * @inheritDoc
     */
    public function addExtension(string $name, $extension = null): ViewInterface
    {
        $this->getEngine()->addExtension($name, $extension);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getEngine(): ViewEngineInterface
    {
        return $this->viewEngine;
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, array $datas = []) : string
    {
        return $this->getEngine()->render($name, $datas);
    }

    /**
     * @inheritDoc
     */
    public function setCacheDir(?string $cacheDir = null): ViewInterface
    {
        $this->getEngine()->setCacheDir($cacheDir);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDirectory(string $directory): ViewInterface
    {
        $this->getEngine()->setDirectory($directory);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setFileExtension(string $fileExtension): ViewInterface
    {
        $this->getEngine()->setFileExtension($fileExtension);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setOverrideDir(string $overrideDir): ViewInterface
    {
        $this->getEngine()->setOverrideDir($overrideDir);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function share($key, $value = null): ViewInterface
    {
        $this->getEngine()->share($key, $value);

        return $this;
    }
}
