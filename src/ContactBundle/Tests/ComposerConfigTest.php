<?php

namespace Perform\ContactBundle\Tests;

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
        return 'perform/contact-bundle';
    }

    protected function getNamespace()
    {
        return 'Perform\ContactBundle';
    }
}
