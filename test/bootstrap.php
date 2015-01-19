<?php

if ( ! is_readable(__DIR__ . '/../vendor/autoload.php')) {
    die (sprintf(
        'Please install dependencies for this package first by running `composer install` in %s.',
        dirname(__DIR__)
    ));
}

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__ . '/../vendor/autoload.php';

//require __DIR__ . '/../vendor/tracy/tracy/src/tracy.php';

$loader->add('ElninoTest', __DIR__ . '/functional');
$loader->add('ElninoTest', __DIR__ . '/unit');
