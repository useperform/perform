<?php

namespace Perform\NotificationBundle\Tests\Recipient;

use Perform\NotificationBundle\Recipient\RecipientInterface;
use Perform\NotificationBundle\Recipient\SimpleRecipient;

/**
 * SimpleRecipientTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SimpleRecipientTest extends \PHPUnit_Framework_TestCase
{
    public function testIsRecipient()
    {
        $this->assertInstanceOf(RecipientInterface::class, new SimpleRecipient(1, 'me@example.com'));
    }

    public function testGetId()
    {
        $r = new SimpleRecipient(1, 'me@example.com');
        $this->assertSame(1, $r->getId());
    }

    public function testGetEmail()
    {
        $r = new SimpleRecipient(1, 'me@example.com');
        $this->assertSame('me@example.com', $r->getEmail());
    }
}
