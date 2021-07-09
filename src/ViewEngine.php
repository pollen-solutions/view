<?php

declare(strict_types=1);

namespace Pollen\View;

use Pollen\Support\Proxy\ContainerProxy;
use Pollen\Support\Proxy\ViewProxy;

abstract class ViewEngine implements ViewEngineInterface
{
    use ContainerProxy;
    use ViewProxy;

    protected array $options = [];

    /**
     * @inheritDoc
     */
    public function setOptions(array $options = []): ViewEngineInterface
    {
        $this->options = $options;

        return $this;
    }
}