#!/usr/bin/env php
<?php

include __DIR__.'/../vendor/autoload.php';

use Perform\Tools\Documentation\DocKernel;
use Perform\Tools\Documentation\FieldTypeReferenceGenerator;
use Perform\Tools\Documentation\SassReferenceGenerator;
use Temping\Temping;

$temp = new Temping();
$kernel = new DocKernel($temp->getDirectory());
$kernel->boot();
$c = $kernel->getContainer();

$twig = $c->get('twig');
$loader = $twig->getLoader();
$loader->addPath(__DIR__.'/../src/Tools/Documentation');

$fieldTypeGenerator = $c->get(FieldTypeReferenceGenerator::class);
$files = $fieldTypeGenerator->generateAllFiles(sprintf('%s/../docs/reference/field-types', __DIR__));

foreach ($files as $file) {
    echo 'Generated '.$file.PHP_EOL;
}

$source = __DIR__.'/../src/BaseBundle/Resources/scss/variables.scss';
$target = __DIR__.'/../docs/reference/sass.rst';
$sassGenerator = $c->get(SassReferenceGenerator::class);
$sassGenerator->generateFile($source, $target);
echo 'Generated '.$target.PHP_EOL;

$temp->reset();
