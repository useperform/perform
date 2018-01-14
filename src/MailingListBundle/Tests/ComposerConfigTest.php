<?php

namespace Perform\MailingListBundle\Tests;

use Perform\BaseBundle\Test\ComposerConfigTestCase;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ComposerConfigTest extends ComposerConfigTestCase
{
    protected function getPackageName()
    {
        return 'perform/mailing-list-bundle';
    }

    protected function getNamespace()
    {
        return 'Perform\MailingListBundle';
    }
}
