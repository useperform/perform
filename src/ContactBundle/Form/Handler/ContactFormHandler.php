<?php

namespace Admin\ContactBundle\Form\Handler;

use Symfony\Component\HttpFoundation\Request;
use Admin\ContactBundle\SpamChecker\SpamCheckerInterface;
use Psr\Log\LoggerInterface;
use Admin\NotificationBundle\Notifier;
use Symfony\Component\Form\FormInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Admin\ContactBundle\Entity\Message;
use Admin\NotificationBundle\RecipientProvider\RecipientProviderInterface;
use Admin\NotificationBundle\Notification;

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

    public function __construct(ObjectManager $entityManager, Notifier $notifier, RecipientProviderInterface $recipientProvider, LoggerInterface $logger = null)
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
        $message->setStatus(Message::STATUS_UNREAD);

        foreach ($this->spamCheckers as $checker) {
            $checker->check($message, $form, $request);
        }

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        if ($message->getStatus() === Message::STATUS_SPAM) {
            //don't notify on spam
            return static::RESULT_SPAM;
        }

        $recipients = $this->recipientProvider->getRecipients([
            'setting' => 'admin_contact_notify_address',
        ]);
        $notification = new Notification($recipients, 'AdminContactBundle:new_message', [
            'subject' => 'New contact form message from '.$message->getName(),
            'message' => $message,
        ]);
        $this->notifier->send($notification, ['email', 'logger']);

        if ($this->logger) {
            $this->logger->info(sprintf('Contact message submitted from %s at %s', $message->getEmail(), $message->getCreatedAt()->format('Y/m/d H:i:s')));
        }

        return static::RESULT_OK;
    }
}
