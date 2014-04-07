#!/usr/bin/env php
<?php

if ( ! is_readable(__DIR__ . '/../vendor/autoload.php')) {
    die (sprintf(
        'Please install dependencies for this package first by running `composer install` in %s.',
        dirname(__DIR__)
    ));
}

$phpunit = __DIR__ . '/../vendor/phpunit/phpunit/phpunit';
$phpunitxml = __DIR__ . '/phpunit.xml';
$dir = __DIR__;

passthru("php $phpunit -c $phpunitxml $dir");
