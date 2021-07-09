<?php

declare(strict_types=1);

namespace Pollen\View\Engines\Plates;

use League\Plates\Engine as BasePlatesEngine;
use LogicException;
use Pollen\Support\Proxy\ContainerProxy;

class PlatesEngine extends BasePlatesEngine
{
    use ContainerProxy;

    /**
     * Template class name.
     * @var string
     */
    protected string $templateClass = PlatesTemplate::class;

    /**
     * @inheritDoc
     */
    public function exists($name): bool
    {
        try {
            return parent::exists($this->getFolders()->exists('_override_dir') ? "_override_dir::$name" : $name);
        } catch (LogicException $e) {
            throw $e;
        }
    }

    /**
     * @param string $name
     *
     * @return PlatesTemplate
     */
    public function make($name): PlatesTemplate
    {
        $regex = <<< REGEXP
        \:\:
        REGEXP;

        if (!preg_match("/$regex/", $name)) {
            $name = $this->getFolders()->exists('_override_dir') ? "_override_dir::$name" : $name;
        }

        $template = new $this->templateClass($this, $name);

        if ($container = $this->getContainer()) {
            $template->setContainer($container);
        }

        return $template;
    }

    /**
     * Set template class name.
     *
     * @param string $templateClass
     *
     * @return static
     */
    public function setTemplateClass(string $templateClass): self
    {
        $this->templateClass = $templateClass;

        return $this;
    }
}