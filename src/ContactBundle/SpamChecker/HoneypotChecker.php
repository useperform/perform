<?php

namespace Perform\ContactBundle\SpamChecker;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Perform\ContactBundle\Entity\Message;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Perform\ContactBundle\Event\HoneypotEvent;
use Perform\ContactBundle\Entity\SpamReport;

/**
 * HoneypotChecker.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class HoneypotChecker implements SpamCheckerInterface
{
    const REPORT_TYPE = 'honeypot';

    protected $entityManager;
    protected $logger;
    protected $caughtForms = [];

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function onHoneypotCaught(HoneypotEvent $event)
    {
        $this->caughtForms[] = $event->getForm();
        $this->caughtForms[] = $event->getRootForm();
    }

    public function check(Message $message, FormInterface $form, Request $request)
    {
        if (!in_array($form, $this->caughtForms)) {
            return;
        }

        $message->setStatus(Message::STATUS_SPAM);

        $report = SpamReport::createFromRequest($request);
        $report->setType(self::REPORT_TYPE);
        $report->setMessage($message);

        $this->entityManager->persist($report);

        $this->logger->notice(sprintf('Spam message found using %s', get_class($this)));
    }
}
