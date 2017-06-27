<?php

namespace Perform\ContactBundle\Form\Handler;

use Symfony\Component\HttpFoundation\Request;
use Perform\ContactBundle\SpamChecker\SpamCheckerInterface;
use Psr\Log\LoggerInterface;
use Perform\NotificationBundle\Notifier\Notifier;
use Symfony\Component\Form\FormInterface;
use Perform\ContactBundle\Entity\Message;
use Perform\NotificationBundle\RecipientProvider\RecipientProviderInterface;
use Perform\NotificationBundle\Notification;
use Doctrine\ORM\EntityManagerInterface;

/**
 * ContactFormHandler.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ContactFormHandler
{
    const RESULT_OK = 1;
    const RESULT_SPAM = 2;

    protected $entityManager;
    protected $notifier;
    protected $recipientProvider;
    protected $logger;
    protected $spamCheckers = [];

    public function __construct(EntityManagerInterface $entityManager, Notifier $notifier, RecipientProviderInterface $recipientProvider, LoggerInterface $logger = null)
    {
        $this->entityManager = $entityManager;
        $this->notifier = $notifier;
        $this->recipientProvider = $recipientProvider;
        $this->logger = $logger;
    }

    public function addSpamChecker(SpamCheckerInterface $checker)
    {
        $this->spamCheckers[] = $checker;
    }

    /**
     * @return mixed A result constant if the form is valid, otherwise false
     */
    public function handleRequest(Request $request, FormInterface $form)
    {
        $form->handleRequest($request);
        if (!$form->isValid()) {
            return false;
        }
        $message = $form->getData();
        $message->setStatus(Message::STATUS_NEW);
        $this->entityManager->persist($message);

        foreach ($this->spamCheckers as $checker) {
            $checker->check($message, $form, $request);
        }

        $this->entityManager->flush();

        if ($message->isSpam()) {
            //don't notify on spam
            return static::RESULT_SPAM;
        }

        $recipients = $this->recipientProvider->getRecipients([
            'setting' => 'perform_contact_notify_address',
        ]);
        $notification = new Notification($recipients, 'PerformContactBundle:new_message', [
            'subject' => 'New contact form message from '.$message->getName(),
            'replyTo' => [$message->getEmail() => $message->getName()],
            'message' => $message,
        ]);
        $this->notifier->send($notification, ['email', 'logger']);

        if ($this->logger) {
            $this->logger->info(sprintf('Contact message submitted from %s at %s', $message->getEmail(), $message->getCreatedAt()->format('Y/m/d H:i:s')));
        }

        return static::RESULT_OK;
    }
}
