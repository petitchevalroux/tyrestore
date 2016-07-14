<?php

use ConfigurationFactory\Factory;
use ConfigurationFactory\Loaders\Php as Loader;
use TyreStore\Di;

$di = Di::getInstance();
$loader = new Loader();
$factory = new Factory();
$factory->setNamespace($di->rootPath . DIRECTORY_SEPARATOR . 'config');
$factory->setLoader($loader);
return $factory;
