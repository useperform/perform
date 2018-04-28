<?php

namespace Perform\SpamBundle\Tests;

use Perform\BaseBundle\Test\ComposerConfigTestCase;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ComposerConfigTest extends ComposerConfigTestCase
{
    protected function getPackageName()
    {
        return 'perform/spam-bundle';
    }

    protected function getNamespace()
    {
        return 'Perform\SpamBundle';
    }
}
