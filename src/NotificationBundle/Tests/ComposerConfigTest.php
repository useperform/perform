<?php

namespace Perform\NotificationBundle\Tests;

use Perform\BaseBundle\Test\ComposerConfigTestCase;

/**
 * ComposerConfigTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ComposerConfigTest extends ComposerConfigTestCase
{
    protected function getPackageName()
    {
        return 'perform/notification-bundle';
    }

    protected function getNamespace()
    {
        return 'Perform\NotificationBundle';
    }
}
