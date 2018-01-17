<?php

namespace Perform\AnalyticsBundle\Tests;

use Perform\BaseBundle\Test\ComposerConfigTestCase;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ComposerConfigTest extends ComposerConfigTestCase
{
    protected function getPackageName()
    {
        return 'perform/analytics-bundle';
    }

    protected function getNamespace()
    {
        return 'Perform\AnalyticsBundle';
    }
}
