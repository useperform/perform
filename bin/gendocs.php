#!/usr/bin/env php
<?php

include __DIR__.'/../vendor/autoload.php';

use Perform\Tools\Documentation\DocGenerator;
use Perform\Tools\Documentation\DocKernel;
use Temping\Temping;

$temp = new Temping();
$kernel = new DocKernel($temp->getDirectory());
$kernel->boot();
$c = $kernel->getContainer();

$registry = $c->get('perform_base.type_registry');

$twig = $c->get('twig');
$loader = $twig->getLoader();
$loader->addPath(__DIR__.'/../src/Tools/Documentation');

$doc = new DocGenerator($twig, $registry);

foreach (array_keys($registry->getAll()) as $name) {
    $file = sprintf('%s/../docs/reference/types/%s.rst', __DIR__, $name);
    $doc->generateFile($name, $file);
    echo 'Generated '.$file.PHP_EOL;
}

$temp->reset();