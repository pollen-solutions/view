<?php

declare(strict_types=1);

namespace Pollen\View\Engines\Plates;

use Pollen\Support\Arr;
use Pollen\Support\Html;
use League\Plates\Template\Template;
use Pollen\Support\Proxy\ContainerProxy;

/**
 * @method string faker(string $formatter, ...$args)
 */
class PlatesTemplate extends Template implements PlatesTemplateInterface
{
    use ContainerProxy;

    /**
     *
     * @var PlatesEngine
     */
    protected $engine;

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, $default = null)
    {
        return Arr::get($this->data, $key, $default);
    }

    /**
     * @inheritDoc
     */
    public function htmlAttrs(?array $attrs = null, bool $linearized = true)
    {
        $attr = $attrs ?? $this->get('attrs', []);

        return $linearized ? Html::attr($attr) : $attr;
    }
}