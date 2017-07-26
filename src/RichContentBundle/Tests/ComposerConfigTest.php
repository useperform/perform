<?php

namespace Perform\RichContentBundle\Tests;

use Perform\BaseBundle\Test\ComposerConfigTestCase;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ComposerConfigTest extends ComposerConfigTestCase
{
    protected function getPackageName()
    {
        return 'perform/rich-content-bundle';
    }

    protected function getNamespace()
    {
        return 'Perform\RichContentBundle';
    }
}
