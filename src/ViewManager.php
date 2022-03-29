<?php

declare(strict_types=1);

namespace Pollen\View;

use Closure;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Exception\ManagerRuntimeException;
use Pollen\Support\Proxy\ContainerProxy;
use Pollen\View\Engines\Plates\PlatesViewEngine;
use Pollen\View\Engines\Twig\TwigViewEngine;
use Pollen\View\Exception\UnableCreateEngineException;
use Pollen\View\Exception\UnableCreateViewException;
use Pollen\View\Extensions\FakerViewExtension;
use Psr\Container\ContainerInterface as Container;
use RuntimeException;
use Throwable;

class ViewManager implements ViewManagerInterface
{
    use BootableTrait;
    use ContainerProxy;

    private static ?ViewManagerInterface $instance = null;

    protected ?ViewInterface $defaultView = null;

    protected string $defaultEngine = 'plates';

    protected array $defaultExtensions = [];

    protected ?string $defaultCacheDir = null;

    protected ?string $defaultDirectory = null;

    protected ?string $defaultFileExtension = null;

    protected ?string $defaultOverrideDir = null;

    protected array $defaultShared = [];

    /**
     * @var array<string, ViewEngineInterface|string>
     */
    protected array $engines = [];

    /**
     * @var array<string, ViewExtensionInterface|callable|string>
     */
    protected array $extensions = [];

    /**
     * @var string[]
     */
    protected array $sharedExtensions = [];

    /**
     * @var array<string, ViewExtensionInterface>
     */
    private array $resolvedExtensions = [];

    /**
     * @param Container|null $container
     */
    public function __construct(?Container $container = null)
    {
        if ($container !== null) {
            $this->setContainer($container);
        }

        $this->boot();

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * Retrieve main class instance.
     *
     * @return static
     */
    public static function getInstance(): ViewManagerInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new ManagerRuntimeException(sprintf('Unavailable [%s] instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        if (!$this->isBooted()) {
            $this->registerEngine('plates', PlatesViewEngine::class);
            $this->registerEngine('twig', TwigViewEngine::class);

            $this->registerExtension('faker', FakerViewExtension::class, true);

            $this->setBooted();
        }
    }

    /**
     * @inheritDoc
     */
    public function createView($viewEngineDef = null, ?Closure $engineCallback = null, bool $withShareExtensions = true): ViewInterface
    {
        if ($viewEngineDef === null) {
            try {
                $viewEngine = $this->resolveEngine();
            } catch (Throwable $e) {
                throw new UnableCreateEngineException('', 0, $e);
            }

        } elseif ($viewEngineDef instanceof ViewEngineInterface) {
            $viewEngine = $viewEngineDef;
        } elseif (is_string($viewEngineDef)) {
            try {
                $viewEngine = $this->resolveEngine($viewEngineDef);
            }  catch (Throwable $e) {
                throw new UnableCreateEngineException('', 0, $e);
            }
        } else {
            throw new UnableCreateEngineException();
        }

        if ($engineCallback !== null) {
            $viewEngine = $engineCallback($viewEngine);
        }

        try {
            $view = new View($viewEngine);
        } catch (Throwable $e) {
            throw new UnableCreateViewException();
        }

        if ($withShareExtensions && ($extensions = $this->sharedExtensions)) {
            foreach ($extensions as $name) {
                $view->addExtension($name, $this->resolveExtension($name));
            }
        }

        return $view;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultView(): ViewInterface
    {
        if ($this->defaultView === null) {
            $this->defaultView = $this->createView();

            $this->defaultView->setDirectory($this->defaultDirectory ?? getcwd());

            if ($this->defaultFileExtension) {
                $this->defaultView->setFileExtension($this->defaultFileExtension);
            }

            if ($this->defaultOverrideDir) {
                $this->defaultView->setOverrideDir($this->defaultOverrideDir);
            }

            if ($this->defaultCacheDir) {
                $this->defaultView->setCacheDir($this->defaultCacheDir);
            }

            if ($this->defaultShared) {
                $this->defaultView->share($this->defaultShared);
            }

            if ($this->defaultExtensions) {
                foreach ($this->defaultExtensions as $name => $extension) {
                    $this->defaultView->addExtension($name, $extension);
                }
            }
        }

        return $this->defaultView;
    }

    /**
     * @inheritDoc
     */
    public function getExtension(string $name)
    {
        try {
           return $this->resolveExtension($name);
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function registerEngine(string $name,  $engineDef, bool $asDefault = false): ViewManagerInterface
    {
        $this->engines[$name] = $engineDef;

        if ($asDefault) {
            $this->defaultEngine = $name;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function registerExtension(string $name, $extensionDef, bool $shared = false): ViewManagerInterface
    {
        $this->extensions[$name] = $extensionDef;

        if ($shared && !in_array($name, $this->sharedExtensions, true)) {
            $this->sharedExtensions[] = $name;
        }

        return $this;
    }

    /**
     * Resolve view engine.
     *
     * @param string|null $name
     *
     * @return ViewEngineInterface
     */
    protected function resolveEngine(?string $name = null): ViewEngineInterface
    {
        if ($name === null) {
            $engineDef = $this->engines[$this->defaultEngine] ?? null;
        } else {
            $engineDef = $this->engines[$name] ?? null;
        }

        if (is_string($engineDef) && $this->containerHas($engineDef)) {
            $engineDef = $this->ContainerGet($engineDef);
        }

        if (!is_a($engineDef, ViewEngineInterface::class, true)) {
            throw new UnableCreateEngineException();
        }

        if (!is_object($engineDef)) {
            /** @var ViewEngineInterface $viewEngine */
            $viewEngine = new $engineDef();
        } else {
            $viewEngine = clone $engineDef;
        }

        if ($container = $this->getContainer()) {
            $viewEngine->setContainer($container);
        }

        return $viewEngine;
    }

    /**
     * Resolve view extension.
     *
     * @param string $name
     *
     * @return ViewExtensionInterface|callable
     */
    protected function resolveExtension(string $name)
    {
        if (!$extensionDef = $this->extensions[$name] ?? null) {
            throw new RuntimeException(sprintf('The view extension [%s] is not registered.', $name));
        }

        if (isset($this->resolvedExtensions[$name])) {
            return $this->resolvedExtensions;
        }

        if (is_string($extensionDef) && $this->containerHas($extensionDef)) {
            $extensionDef = $this->ContainerGet($extensionDef);
        }

        if (is_a($extensionDef, ViewExtensionInterface::class, true)) {
            if (!is_object($extensionDef)) {
                $extension = new $extensionDef();
            } else {
                $extension = $extensionDef;
            }

            $extension->setName($name);

            if ($container = $this->getContainer()) {
                $extension->setContainer($container);
            }

            return $extension;
        }

        if (is_callable($extensionDef)) {
            return $extensionDef;
        }

        throw new RuntimeException(sprintf('The view extension [%s] definition is invalid.', $name));
    }

    /**
     * @inheritDoc
     */
    public function setDefaultEngine(string $name): ViewManagerInterface
    {
        $this->defaultEngine = $name;
        $this->defaultView = null;

        return $this;
    }

    // View Methods
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function addExtension(string $name, $extension = null): ViewInterface
    {
        $this->defaultExtensions[$name] = $extension;

        $this->getDefaultView()->addExtension($name, $extension);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getEngine(): ViewEngineInterface
    {
        return $this->getDefaultView()->getEngine();
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, array $datas = []): string
    {
        return $this->getDefaultView()->render($name, $datas);
    }

    /**
     * @inheritDoc
     */
    public function setCacheDir(?string $cacheDir = null): ViewInterface
    {
        $this->defaultCacheDir = $cacheDir;

        $this->getDefaultView()->setCacheDir($cacheDir);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDirectory(string $directory): ViewInterface
    {
        $this->defaultDirectory = $directory;

        $this->getDefaultView()->setDirectory($directory);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setFileExtension(string $fileExtension): ViewInterface
    {
        $this->defaultFileExtension = $fileExtension;

        $this->getDefaultView()->setFileExtension($fileExtension);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setOverrideDir(string $overrideDir): ViewInterface
    {
        $this->defaultOverrideDir = $overrideDir;

        $this->getDefaultView()->setOverrideDir($overrideDir);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function share($key, $value = null): ViewInterface
    {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach($keys as $k => $v) {
            $this->defaultShared[$k] =  $v;
        }

        $this->getDefaultView()->share($key, $value);

        return $this;
    }
}
