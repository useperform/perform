<?php

namespace Perform\ContactBundle\Form\Handler;

use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Perform\NotificationBundle\Notifier\NotifierInterface;
use Symfony\Component\Form\FormInterface;
use Perform\ContactBundle\Entity\Message;
use Perform\NotificationBundle\RecipientProvider\RecipientProviderInterface;
use Perform\NotificationBundle\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Perform\SpamBundle\SpamManager;

/**
 * Handle contact form submissions, save the message, detect spam, and
 * send notifications.
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
    protected $spamManager;
    protected $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        NotifierInterface $notifier,
        RecipientProviderInterface $recipientProvider,
        SpamManager $spamManager,
        LoggerInterface $logger = null)
    {
        $this->entityManager = $entityManager;
        $this->notifier = $notifier;
        $this->recipientProvider = $recipientProvider;
        $this->spamManager = $spamManager;
        $this->logger = $logger;
    }

    /**
     * @return mixed A result constant if the form is valid, otherwise false
     */
    public function handleRequest(Request $request, FormInterface $form)
    {
        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            return false;
        }
        $message = $form->getData();

        $result = $this->spamManager->checkForm($form);
        $result->mergeReports($this->spamManager->checkText($message->getMessage()));

        if ($result->isSpam()) {
            $message->setStatus(Message::STATUS_SPAM);
            foreach ($result->getReports() as $report) {
                $message->addSpamReport($report);
            }
        } else {
            $message->setStatus(Message::STATUS_NEW);
        }

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        if ($result->isSpam()) {
            //don't notify on spam
            return static::RESULT_SPAM;
        }

        if ($this->logger) {
            $this->logger->info(sprintf('Contact message submitted from %s at %s', $message->getEmail(), $message->getCreatedAt()->format('Y/m/d H:i:s')));
        }

        $this->sendNotifications($message);

        return static::RESULT_OK;
    }

    /**
     * @param Message $message
     */
    public function sendNotifications(Message $message)
    {
        $recipients = $this->recipientProvider->getRecipients([
            'setting' => 'perform_contact_notify_address',
        ]);
        $notification = new Notification($recipients, 'PerformContactBundle:new_message', [
            'subject' => 'New contact form message from '.$message->getName(),
            'replyTo' => [$message->getEmail() => $message->getName()],
            'message' => $message,
        ]);
        $this->notifier->send($notification, ['email']);
    }
}
