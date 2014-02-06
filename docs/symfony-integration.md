# Symfony Integration

## bootstrapping

You can use the bootloader to load the `Webforge\ProjectStack\BootContainer`. 

```php
use Psc\Boot\BootLoader;

require_once __DIR__.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'package.boot.php';

$bootLoader = new BootLoader(__DIR__, 'Webforge\ProjectStack\BootContainer');
$bootLoader->loadComposer();
$bootLoader->registerPackageRoot();

$container = $bootLoader->registerContainer();
$container->registerDoctrineAnnotations();
```

You can retrieve the Kernel with calling `$conatainer->getKernel()`.

## services in symfony configuration

integrate the projectstack services in your config.yml file:

```yaml
imports:
    - { resource: parameters.yml }
    - { resource: security.yml }
    - { resource: "../../vendor/webforge/project-stack/etc/symfony/services.yml" }
```

All services are prefixed with `projectstack.` You can look into the etc/symfony/services.yml in this repository to find the names of the common services you might need in your controllers and define a controller as a service like this:

```yaml
services:
  # ...

  yield.controller.page:
    class:     Webforge\Yield\Controller\PageController
    arguments: ["@projectstack.entity_manager", "@projectstack.template_engine"]

```

## cli

When you're using the bootstrap above and your cli.php is located in `bin/` next to the bootstrap.php it could look like this:

```php
#!/usr/bin/env php
<?php

$bootContainer = require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'bootstrap.php';

$application = $bootContainer->get('projectstack.cli.application');
$application->run();

?>
```

## frontcontroller (index.php)

The standard symfony frontcontroller could look like this:

```php
<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

$container = require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'bootstrap.php';

Debug::enable(); // this has problems with autoloading from different locations

$request = Request::createFromGlobals();

$kernel = $container->getKernel();
$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
?>
```


## customizing the kernel

You can extend the Kernel with a Kernel class in the main namespace of your project.

```php
<?php

namespace ACME\MyProject;

class Kernel extends \Webforge\ProjectStack\Symfony\Kernel {

  public function registerBundles() {
    $bundles = parent::registerBundles();

    $bundles[] = new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle();
    $bundles[] = new \JMS\SerializerBundle\JMSSerializerBundle();
    $bundles[] = new \FOS\UserBundle\FOSUserBundle();
    $bundles[] = new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle();
    $bundles[] = new \Symfony\Bundle\TwigBundle\TwigBundle();

    return $bundles;
  }
}
?>
```
You can find out the right namespace with `$container->getProject()->getNamespace()`;

That's it. 