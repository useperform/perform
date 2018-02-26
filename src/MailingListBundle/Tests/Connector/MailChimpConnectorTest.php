<?php

namespace Perform\MailingListBundle\Tests\Connector;

use Perform\MailingListBundle\Connector\MailChimpConnector;
use Psr\Log\LoggerInterface;
use DrewM\MailChimp\MailChimp;
use Perform\MailingListBundle\Entity\Subscriber;
use Perform\MailingListBundle\Exception\ListNotFoundException;
use Perform\MailingListBundle\Exception\ConnectorException;
use Perform\MailingListBundle\SubscriberFields;

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
        $subscriber->setAttributes([
            SubscriberFields::FIRST_NAME => 'Test',
            SubscriberFields::LAST_NAME => 'User',
        ]);
        $hash = md5('test@example.com');

        $this->mc->expects($this->once())
            ->method('put')
            ->with('lists/some-mailchimp-list/members/'.$hash, [
                'email_address' => 'test@example.com',
                'status' => 'subscribed',
                'merge_fields' => [
                    'FNAME' => 'Test',
                    'LNAME' => 'User',
                ],
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
                'merge_fields' => [],
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
                'merge_fields' => [],
            ])
            ->will($this->returnValue(['status' => 401]));

        $this->setExpectedException(ConnectorException::class);
        $this->connector->subscribe($subscriber);
    }

    public function testMergeFieldsAreParsed()
    {
        $subscriber = new Subscriber();
        $this->assertSame([], $this->connector->createMergeFields($subscriber));

        $subscriber->setAttribute(SubscriberFields::FIRST_NAME, 'Test');
        $this->assertSame(['FNAME' => 'Test'], $this->connector->createMergeFields($subscriber));

        $subscriber->setAttribute(SubscriberFields::LAST_NAME, 'User');
        $this->assertSame(['FNAME' => 'Test', 'LNAME' => 'User'], $this->connector->createMergeFields($subscriber));
    }
}
