<?php

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require_once __DIR__ . '/../vendor/autoload.php';

$loader->add('ElninoTest', __DIR__ . '/functional');
$loader->add('ElninoTest', __DIR__ . '/unit');

