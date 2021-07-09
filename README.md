# View Component

[![Latest Version](https://img.shields.io/badge/release-1.0.0-blue?style=for-the-badge)](https://www.presstify.com/pollen-solutions/view/)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-green?style=for-the-badge)](LICENSE.md)
[![PHP Supported Versions](https://img.shields.io/badge/PHP->=7.4-8892BF?style=for-the-badge&logo=php)](https://www.php.net/supported-versions.php)

Pollen Solutions **View** Component is a template engine system.

This is an expandable display template engine that natively integrates **Plates** and **Twig** library.

## Installation

```bash
composer require pollen-solutions/view
```

## Fundamentals

### About Plates

**Plates** is a native PHP template system that’s fast, easy to use.
It’s inspired by the [Twig](#about-twig) template engine.
Plates is designed for developers who prefer to use native PHP templates over compiled template languages.

Plates is use as default engine in the Pollen Solutions Components Suite.

More informations :
- [Plates official documentation](https://platesphp.com/)

### About Twig

**Twig** is the templating engine that included with [Symfony Framework](https://symfony.com/).
**Twig** is a modern template engine for PHP.
**Twig** compiles templates down to plain optimized PHP code.

**Twig** is natively included with Pollen View component.

More informations :
- [Twig official documentation](https://twig.symfony.com/doc/)

### Third-party Engine

#### Blade

**Blade** is the templating engine that included with [Laravel Framework](https://laravel.com/).

**Blade** is not natively included with the Pollen View component, but can easily be added :

```bash
composer require pollen-solutions/view-blade
```

More informations :
- [Blade official documentation](https://laravel.com/docs/8.x/blade)

#### Mustache

Mustache PHP engine is currently in project and coming soon.

More informations :
- [Mustache official documentation](https://github.com/bobthecow/mustache.php/wiki)

## Fundamentals

### An unified API interface

To respond to the particularity of each of the model display engines, Pollen View benefits from a unified interface
this makes it possible to work with different engines via the same API.

### Directory and override

Pollen **View** purposes a different logic from the libraries it inherits.

Each template file included in the template directory can be replaced by a template file with the same name in the 
override directory.

### Extending the template engine

Pollen **View** also makes it possible to extend the functionalities of the template display engines through an 
easy interface.

### Caching 

To facilitate the work of application development, Pollen View allows you to disable the cache of the display template 
engines that it implements.

It is strongly recommended that you enable the cache when deploying to production.

## Using global View

#### Template file

```php
# /var/www/html/views/hello-world.plates.php
echo 'Hello World !';
```

#### View call

```php
use Pollen\View\ViewManager;

$viewManager = new ViewManager();

$viewManager->setDirectory('/var/www/html/views');

echo $view->render('hello-world');
exit;
```

## Creating a new view instance

### Simple usage

#### Template file

```php
# /var/www/html/views/hello-world.plates.php
echo 'Hello World' . $this->get('name') . '!';
```

#### View call 

```php
use Pollen\View\ViewManager;

$viewManager = new ViewManager();

## Creating a Plates View
$view = $viewManager->createView('plates')->setDirectory('/var/www/html/views');

echo $view->render('hello-world', ['name' => 'John Doe']);
exit;
```

### Advanced usage 

In this example we use a customized template class and the view is configured through the view engine callback. 

#### Customized template class

```php
namespace Acme\View;

use Pollen\View\Engines\Plates\PlatesTemplate as BasePlatesTemplate;

class PlatesTemplate extends BasePlatesTemplate
{
    public function helloWorldName(string $name): string
    {
        return 'Hello World '. $name . '!';
    }
}
```

#### Template file

```php
# /var/www/html/views/hello-world.plates.php
/**
 * @var Acme\View\PlatesTemplate $this
 */
echo $this->helloWorldName($this->get('name'));
```

#### View call

```php
use Pollen\View\ViewManager;
use Pollen\View\Engines\Plates\PlatesViewEngine;
use Acme\View\PlatesTemplate;

$viewManager = new ViewManager();

$directory = '/var/www/html/views';

$view = $viewManager->createView(
    (new PlatesViewEngine()),
    function (PlatesViewEngine $platesViewEngine) use ($directory) {
        $platesViewEngine->platesEngine()
            ->setDirectory($directory);

        $platesViewEngine->platesEngine()->setTemplateClass(PlatesTemplate::class);

        return $platesViewEngine;
    }
);

echo $view->render('hello-world', ['name' => 'John Doe']);
exit;
```

## Extending a View

### Simple method (with a callback)

#### Template file

```php
# /var/www/html/views/hello-world.plates.php
echo $this->helloWorldName($this->get('name'));
```

#### View call

```php
use Pollen\View\ViewManager;

$viewManager = new ViewManager();

$view = $viewManager->createView('plates')
    ->setDirectory('/var/www/html/views')
    ->addExtension('helloWorldName', function (string $name): string {
        return sprinf('Hello World %s !', $name);
    });

echo $view->render('hello-world', ['name' => 'John Doe']);
exit;
```

### Advanced method with View Extension class

#### View Extension class

```php
use Acme\View;

use Pollen\View\ViewExtension;
use Pollen\View\ViewEngineInterface;
use Pollen\View\Engines\Plates\PlatesViewEngine;
use Pollen\View\Engines\Twig\TwigViewEngine;
use Twig\TwigFunction;

class HelloWorldNameViewExtension extends ViewExtension
{
    public function register(ViewEngineInterface $viewEngine)
    {
        if (is_a($viewEngine, PlatesViewEngine::class)) {
            $viewEngine->platesEngine()->registerFunction(
                    $this->getName(),
                    function (string $name): string {
                        return sprinf('Hello World %s !', $name);
                    }
                );
        }
        
        /**
         * Extending Twig
         * @see https://twig.symfony.com/doc/3.x/advanced.html 
         */
        if (is_a($viewEngine, TwigViewEngine::class)) {
            $viewEngine->twigEnvironment()->addFunction(
                new TwigFunction(
                    $this->getName(),
                    function (string $name): string {
                        return sprinf('Hello World %s !', $name);
                    }
                )
            );
        }
           
        return null;
    }
}

```

#### Template file

```php
# /var/www/html/views/hello-world.plates.php
echo $this->helloWorldName($this->get('name'));
```

#### View call

```php
use Pollen\View\ViewManager;
use Acme\View\HelloWorldNameViewExtension;

$viewManager = new ViewManager();

$view = $viewManager->createView('plates')
    ->setDirectory('/var/www/html/views')
    ->addExtension('helloWorldName', new HelloWorldNameViewExtension());

echo $view->render('hello-world', ['name' => 'John Doe']);
exit;
```
