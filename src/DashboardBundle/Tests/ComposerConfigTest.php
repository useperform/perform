<?php

namespace Perform\DashboardBundle\Tests;

use Perform\BaseBundle\Test\ComposerConfigTestCase;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ComposerConfigTest extends ComposerConfigTestCase
{
    protected function getPackageName()
    {
        return 'perform/dashboard-bundle';
    }

    protected function getNamespace()
    {
        return 'Perform\DashboardBundle';
    }
}
