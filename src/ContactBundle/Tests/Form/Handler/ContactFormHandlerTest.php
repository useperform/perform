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
use Perform\SpamBundle\SpamManager;
use Perform\SpamBundle\Checker\CheckResult;
use Perform\SpamBundle\Entity\Report;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ContactFormHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected $entityManager;
    protected $notifier;
    protected $recipientProvider;
    protected $spamManager;
    protected $handler;

    public function setUp()
    {
        $this->entityManager = $this->getMock(EntityManagerInterface::class);
        $this->notifier = $this->getMock(Notifier::class);
        $this->recipientProvider = $this->getMock(RecipientProviderInterface::class);
        $this->spamManager = $this->getMockBuilder(SpamManager::class)
                           ->disableOriginalConstructor()
                           ->getMock();
        $this->handler = new ContactFormHandler($this->entityManager, $this->notifier, $this->recipientProvider, $this->spamManager);
    }

    public function testHandleSpam()
    {
        $message = new Message();
        $message->setMessage('Spam text');
        $form = $this->getMock(FormInterface::class);
        $form->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->any())
            ->method('isSubmitted')
            ->will($this->returnValue(true));
        $form->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($message));
        $this->recipientProvider->expects($this->any())
            ->method('getRecipients')
            ->will($this->returnValue([]));

        $formResult = new CheckResult();
        $formResult->addReport($r1 = new Report());
        $this->spamManager->expects($this->once())
            ->method('checkForm')
            ->with($form)
            ->will($this->returnValue($formResult));
        $textResult = new CheckResult();
        $textResult->addReport($r2 = new Report());
        $this->spamManager->expects($this->once())
            ->method('checkText')
            ->with('Spam text')
            ->will($this->returnValue($textResult));

        $this->assertSame(ContactFormHandler::RESULT_SPAM, $this->handler->handleRequest(new Request(), $form));
        $this->assertSame(Message::STATUS_SPAM, $message->getStatus());
        $this->assertSame([$r1, $r2], $message->getSpamReports()->toArray());
    }
}
