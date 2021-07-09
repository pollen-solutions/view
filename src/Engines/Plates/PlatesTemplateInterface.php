<?php

declare(strict_types=1);

namespace Pollen\View\Engines\Plates;

/**
 * @mixin \League\Plates\Template\Template
 * @method string faker(string $formatter, ...$args)
 */
interface PlatesTemplateInterface
{
    /**
     * Get all parameters.
     *
     * @return array
     */
    public function all(): array;

    /**
     * Get parameter for a given key.
     *
     * @param string $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Get list|Linearized HTML attributes.
     *
     * @param array|null $attrs
     * @param bool $linearized
     *
     * @return string|array
     */
    public function htmlAttrs(?array $attrs = null, bool $linearized = true);
}