<?php

/**
 * Bootstrap and Autoload whole application
 *
 * you can use this file to bootstrap for tests or bootstrap for scripts / others
 */
$autoLoader = require 'vendor/autoload.php';

$container = new \Webforge\ProjectStack\BootContainer(__DIR__);
$container->registerGlobal();
$container->setAutoLoader($autoLoader);
$container->init();

return $container;
