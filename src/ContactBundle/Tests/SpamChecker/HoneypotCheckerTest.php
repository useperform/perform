<?php

namespace Perform\ContactBundle\Tests\SpamChecker;

use Perform\ContactBundle\Entity\Message;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Perform\ContactBundle\SpamChecker\HoneypotChecker;
use Perform\ContactBundle\Event\HoneypotEvent;
use Perform\ContactBundle\Entity\SpamReport;

/**
 * HoneypotCheckerTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class HoneypotCheckerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->logger = $this->getMock(LoggerInterface::class);
        $this->entityManager = $this->getMock(EntityManagerInterface::class);
        $this->checker = new HoneypotChecker($this->entityManager, $this->logger);
    }

    public function testCheckGoodMessage()
    {
        $message = new Message();
        $form = $this->getMock(FormInterface::class);
        $this->entityManager->expects($this->never())
            ->method('persist');
        $this->checker->check($message, $form, new Request());
    }

    public function testCheckBadMessage()
    {
        $message = new Message();
        $form = $this->getMock(FormInterface::class);
        $this->checker->onHoneypotCaught(new HoneypotEvent($form));
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($report) {
                return $report instanceof SpamReport;
            }));
        $this->entityManager->expects($this->never())
            ->method('flush');
        $this->checker->check($message, $form, new Request());
    }
}
