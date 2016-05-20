<?php

namespace Admin\MailingListBundle\Tests\Entity;

use Admin\MailingListBundle\Entity\Subscriber;

/**
 * SubscriberTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SubscriberTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFullname()
    {
        $subscriber = new Subscriber();
        $subscriber->setForename('Glynn');
        $subscriber->setSurname('Forrest');
        $this->assertSame('Glynn Forrest', $subscriber->getFullname());
    }
}
