<?php

namespace Perform\PageEditorBundle\Tests;

use Perform\BaseBundle\Test\ComposerConfigTestCase;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ComposerConfigTest extends ComposerConfigTestCase
{
    protected function getPackageName()
    {
        return 'perform/page-editor-bundle';
    }

    protected function getNamespace()
    {
        return 'Perform\PageEditorBundle';
    }
}
