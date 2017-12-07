#!/usr/bin/env php
<?php

include __DIR__.'/../vendor/autoload.php';

use Perform\Tools\Documentation\DocKernel;
use Perform\Tools\Documentation\TypeReferenceGenerator;
use Perform\Tools\Documentation\SassReferenceGenerator;
use Temping\Temping;

$temp = new Temping();
$kernel = new DocKernel($temp->getDirectory());
$kernel->boot();
$c = $kernel->getContainer();

$registry = $c->get('perform_base.type_registry');

$twig = $c->get('twig');
$loader = $twig->getLoader();
$loader->addPath(__DIR__.'/../src/Tools/Documentation');

$types = new TypeReferenceGenerator($twig, $registry);

foreach (array_keys($registry->getAll()) as $name) {
    $file = sprintf('%s/../docs/reference/types/%s.rst', __DIR__, $name);
    $types->generateFile($name, $file);
    echo 'Generated '.$file.PHP_EOL;
}

$source = __DIR__.'/../src/BaseBundle/Resources/scss/variables.scss';
$target = __DIR__.'/../docs/reference/sass.rst';
$sass = new SassReferenceGenerator($twig);
$sass->generateFile($source, $target);
echo 'Generated '.$target.PHP_EOL;

$temp->reset();
