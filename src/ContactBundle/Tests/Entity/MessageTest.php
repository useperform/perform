<?php

namespace Perform\ContactBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Perform\ContactBundle\Entity\Message;
use Perform\SpamBundle\Entity\Report;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MessageTest extends TestCase
{
    public function testIsSpam()
    {
        $m = new Message();
        $this->assertFalse($m->isSpam());
        $m->setStatus(Message::STATUS_NEW);
        $this->assertFalse($m->isSpam());
        $m->setStatus(Message::STATUS_ARCHIVE);
        $this->assertFalse($m->isSpam());
        $m->setStatus(Message::STATUS_SPAM);
        $this->assertTrue($m->isSpam());
    }

    public function testAddAndRemoveSpamReports()
    {
        $m = new Message();
        $m->addSpamReport($r1 = new Report());
        $m->addSpamReport($r2 = new Report());
        $m->removeSpamReport($r1);

        $this->assertSame([$r2], array_values($m->getSpamReports()->toArray()));
    }
}
