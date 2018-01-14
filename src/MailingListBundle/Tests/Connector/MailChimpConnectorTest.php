<?php

namespace Perform\MailingListBundle\Tests\Connector;

use Perform\MailingListBundle\Connector\MailChimpConnector;
use Psr\Log\LoggerInterface;
use DrewM\MailChimp\MailChimp;
use Perform\MailingListBundle\Entity\Subscriber;
use Perform\MailingListBundle\Exception\ListNotFoundException;
use Perform\MailingListBundle\Exception\ConnectorException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MailChimpConnectorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->mc = $this->getMockBuilder(MailChimp::class)
                  ->disableOriginalConstructor()
                  ->setMethods(['put'])
                  ->getMock();
        $this->logger = $this->getMock(LoggerInterface::class);
        $this->connector = new MailChimpConnector($this->mc, $this->logger);
    }

    public function testSubscribe()
    {
        $subscriber = new Subscriber();
        $subscriber->setEmail('test@example.com')
            ->setList('some-mailchimp-list');
        $hash = md5('test@example.com');

        $this->mc->expects($this->once())
            ->method('put')
            ->with('lists/some-mailchimp-list/members/'.$hash, [
                'email_address' => 'test@example.com',
                'status' => 'subscribed',
            ])
            ->will($this->returnValue(['status' => 'subscribed']));

        $this->connector->subscribe($subscriber);
    }

    public function testSubscribeListNotFound()
    {
        $subscriber = new Subscriber();
        $subscriber->setEmail('test@example.com')
            ->setList('some-unknown-list');
        $hash = md5('test@example.com');

        $this->mc->expects($this->once())
            ->method('put')
            ->with('lists/some-unknown-list/members/'.$hash, [
                'email_address' => 'test@example.com',
                'status' => 'subscribed',
            ])
            ->will($this->returnValue(['status' => 404]));

        $this->setExpectedException(ListNotFoundException::class);
        $this->connector->subscribe($subscriber);
    }

    public function testSubscribeError()
    {
        $subscriber = new Subscriber();
        $subscriber->setEmail('test@example.com')
            ->setList('some-mailchimp-list');
        $hash = md5('test@example.com');

        $this->mc->expects($this->once())
            ->method('put')
            ->with('lists/some-mailchimp-list/members/'.$hash, [
                'email_address' => 'test@example.com',
                'status' => 'subscribed',
            ])
            ->will($this->returnValue(['status' => 401]));

        $this->setExpectedException(ConnectorException::class);
        $this->connector->subscribe($subscriber);
    }
}
