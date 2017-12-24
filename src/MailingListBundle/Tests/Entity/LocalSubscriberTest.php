<?php

namespace Perform\MailingListBundle\Tests\Entity;

use Perform\MailingListBundle\Entity\LocalSubscriber;
use Perform\MailingListBundle\Entity\LocalList;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LocalSubscriberTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeAddedToLists()
    {
        $sub = new LocalSubscriber();
        $list = new LocalList();
        $sub->addList($list);
        $this->assertSame($list, $sub->getLists()[0]);
        $sub->removeList($list);
        $this->assertSame(0, $sub->getLists()->count());
    }

    public function testSameListIsNotAddedTwice()
    {
        $sub = new LocalSubscriber();
        $list = new LocalList();
        $sub->addList($list);
        $sub->addList($list);
        $sub->addList($list);
        $this->assertSame(1, $sub->getLists()->count());
    }
}
