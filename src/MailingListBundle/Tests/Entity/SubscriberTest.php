<?php

namespace Perform\MailingListBundle\Tests\Entity;

use Perform\MailingListBundle\Entity\Subscriber;
use Perform\MailingListBundle\Exception\MissingAttributeException;

/**
 * SubscriberTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SubscriberTest extends \PHPUnit_Framework_TestCase
{
    public function testGetEmail()
    {
        $s = new Subscriber();
        $s->setEmail('person@example.com');
        $this->assertSame('person@example.com', $s->getEmail());
    }

    public function testEmailToLower()
    {
        $s = new Subscriber();
        $s->setEmail('DrNapoleon.Hughes@example.com');
        $this->assertSame('drnapoleon.hughes@example.com', $s->getEmail());
    }

    public function testGetAttributes()
    {
        $s = new Subscriber();
        $this->assertSame([], $s->getAttributes());

        $s = new Subscriber();
        $attr = ['first_name' => 'Glynn'];
        $s->setAttributes($attr);
        $this->assertSame($attr, $s->getAttributes());
    }


    public function testGetSingleAttribute()
    {
        $s = new Subscriber();
        $s->setAttribute('foo', 'value');
        $this->assertSame('value', $s->getAttribute('foo'));
    }

    public function testGetUnknownAttribute()
    {
        $s = new Subscriber();
        $this->setExpectedException(MissingAttributeException::class);
        $s->getAttribute('unknown');
    }
}
