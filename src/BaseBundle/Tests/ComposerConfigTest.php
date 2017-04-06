<?php

namespace Perform\BaseBundle\Tests;

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
        return 'perform/base-bundle';
    }

    protected function getNamespace()
    {
        return 'Perform\BaseBundle';
    }
}
