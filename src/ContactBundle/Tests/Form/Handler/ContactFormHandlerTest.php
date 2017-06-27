<?php

namespace Perform\ContactBundle\Tests\Form\Handler;

use Perform\NotificationBundle\Notifier\Notifier;
use Perform\NotificationBundle\RecipientProvider\RecipientProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Perform\ContactBundle\SpamChecker\SpamCheckerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Perform\ContactBundle\Entity\Message;
use Perform\ContactBundle\Form\Handler\ContactFormHandler;

/**
 * ContactFormHandlerTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ContactFormHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected $entityManager;
    protected $notifier;
    protected $recipientProvider;
    protected $handler;

    public function setUp()
    {
        $this->entityManager = $this->getMock(EntityManagerInterface::class);
        $this->notifier = $this->getMock(Notifier::class);
        $this->recipientProvider = $this->getMock(RecipientProviderInterface::class);
        $this->handler = new ContactFormHandler($this->entityManager, $this->notifier, $this->recipientProvider);
    }

    public function testSpamCheckersAreCalled()
    {
        $checker = $this->getMock(SpamCheckerInterface::class);
        $this->handler->addSpamChecker($checker);
        $message = new Message();
        $form = $this->getMock(FormInterface::class);
        $form->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($message));
        $this->recipientProvider->expects($this->any())
            ->method('getRecipients')
            ->will($this->returnValue([]));

        $request = new Request();

        $checker->expects($this->once())
            ->method('check')
            ->with($message, $form, $request);

        $this->handler->handleRequest($request, $form);
    }
}
