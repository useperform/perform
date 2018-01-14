<?php

namespace Perform\Licensing\Tests;

use Perform\BaseBundle\Test\ComposerConfigTestCase;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ComposerConfigTest extends ComposerConfigTestCase
{
    protected function getPackageName()
    {
        return 'perform/licensing';
    }

    protected function getNamespace()
    {
        return 'Perform\Licensing';
    }
}
