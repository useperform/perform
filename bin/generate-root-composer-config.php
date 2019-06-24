#!/usr/bin/env php
<?php

// autoloading not available, this script is used to generate it
include __DIR__.'/../src/Tools/ComposerConfigMerger.php';

use Perform\Tools\ComposerConfigMerger;

$merger = new ComposerConfigMerger();
$packages = [
    'AnalyticsBundle',
    'BaseBundle',
    'ContactBundle',
    'DashboardBundle',
    'DevBundle',
    'Licensing',
    'MailingListBundle',
    'MediaBundle',
    'NotificationBundle',
    'PageEditorBundle',
    'RichContentBundle',
    'SpamBundle',
    'UserBundle',
];
foreach ($packages as $package) {
    $merger->loadFile(sprintf('%s/../src/%s/composer.json', __DIR__, $package), $package);
}

$merger->dumpFile(__DIR__.'/../composer.json');
