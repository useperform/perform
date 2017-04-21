<?php

namespace Perform\DevBundle\Tests;

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
        return 'perform/dev-bundle';
    }

    protected function getNamespace()
    {
        return 'Perform\DevBundle';
    }
}
