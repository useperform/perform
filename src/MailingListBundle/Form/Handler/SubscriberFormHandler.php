<?php

namespace Admin\MailingListBundle\Form\Handler;

use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * SubscriberFormHandler.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SubscriberFormHandler
{
    const RESULT_SUBSCRIBED = 1;
    const RESULT_ALREADY_SUBSCRIBED = 2;

    public function __construct(ObjectManager $entityManager, LoggerInterface $logger = null)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function handleRequest(Request $request, FormInterface $form)
    {
        $form->handleRequest($request);
        if (!$form->isValid()) {
            return false;
        }
        $subscriber = $form->getData();
        $subscriber->setEnabled(true);

        $existing = $this->entityManager
                  ->getRepository('AdminMailingListBundle:Subscriber')
                  ->findOneBy(['email' => $subscriber->getEmail()]);
        if ($existing) {
            return self::RESULT_ALREADY_SUBSCRIBED;
        }

        $this->entityManager->persist($subscriber);
        $this->entityManager->flush();

        if ($this->logger) {
            $this->logger->info(sprintf('New mailing list subscriber "%s"', $subscriber->getEmail()));
        }

        return static::RESULT_SUBSCRIBED;
    }
}
