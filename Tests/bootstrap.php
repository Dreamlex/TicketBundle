<?php

use Doctrine\Common\Annotations\AnnotationRegistry; //Только если вы используете Doctrine и анотации
use Composer\Autoload\ClassLoader;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

AnnotationRegistry::registerLoader(array($loader, 'loadClass')); //Только если вы используете Doctrine и аннотации

return $loader;