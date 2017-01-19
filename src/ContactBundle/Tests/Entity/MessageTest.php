<?php

namespace Perform\ContactBundle\Tests\Entity;

use Perform\ContactBundle\Entity\Message;

/**
 * MessageTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MessageTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSpam()
    {
        $m = new Message();
        $this->assertFalse($m->isSpam());
        $m->setStatus(Message::STATUS_UNREAD);
        $this->assertFalse($m->isSpam());
        $m->setStatus(Message::STATUS_SPAM);
        $this->assertTrue($m->isSpam());
    }
}
