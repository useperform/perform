<?php

namespace Perform\BlogBundle\Tests;

use Perform\BaseBundle\Test\ComposerConfigTestCase;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ComposerConfigTest extends ComposerConfigTestCase
{
    protected function getPackageName()
    {
        return 'perform/blog-bundle';
    }

    protected function getNamespace()
    {
        return 'Perform\BlogBundle';
    }
}
