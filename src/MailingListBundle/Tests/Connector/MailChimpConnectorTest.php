<?php

namespace Perform\MailingListBundle\Tests\Connector;

use Perform\MailingListBundle\Connector\MailChimpConnector;
use Psr\Log\LoggerInterface;
use DrewM\MailChimp\MailChimp;
use Perform\MailingListBundle\Entity\Subscriber;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MailChimpConnectorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->mc = $this->getMockBuilder(MailChimp::class)
                  ->disableOriginalConstructor()
                  ->getMock();
        $this->logger = $this->getMock(LoggerInterface::class);
        $this->connector = new MailChimpConnector($this->mc, $this->logger);
    }

    public function testSubscribe()
    {
        $subscriber = new Subscriber();
        $subscriber->setEmail('test@example.com')
            ->setList('some-mailchimp-list');

        $this->mc->expects($this->once())
            ->method('post')
            ->with('lists/some-mailchimp-list/members', [
                'email_address' => 'test@example.com',
                'status' => 'subscribed',
            ]);
        $this->connector->subscribe($subscriber);
    }
}
