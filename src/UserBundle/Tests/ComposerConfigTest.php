<?php

namespace Perform\UserBundle\Tests;

use Perform\BaseBundle\Test\ComposerConfigTestCase;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ComposerConfigTest extends ComposerConfigTestCase
{
    protected function getPackageName()
    {
        return 'perform/user-bundle';
    }

    protected function getNamespace()
    {
        return 'Perform\UserBundle';
    }
}
