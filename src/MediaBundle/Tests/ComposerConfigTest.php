<?php

namespace Perform\MediaBundle\Tests;

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
        return 'perform/media-bundle';
    }

    protected function getNamespace()
    {
        return 'Perform\MediaBundle';
    }
}
