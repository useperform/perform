<?php

namespace Perform\MailingListBundle\Enricher;

use Perform\MailingListBundle\Entity\Subscriber;
use Perform\MailingListBundle\SubscriberFields;
use Perform\UserBundle\Repository\UserRepository;

/**
 * Add first and last name to subscribers if they exist in the perform user table.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UserEnricher implements EnricherInterface
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function enrich(array $subscribers)
    {
        $subsByEmail = [];
        foreach ($subscribers as $subscriber) {
            $subsByEmail[$subscriber->getEmail()] = $subscriber;
        }

        $users = $this->userRepository->findByEmails(array_keys($subsByEmail));
        foreach ($users as $user) {
            $subscriber = $subsByEmail[$user->getEmail()];
            $subscriber->setAttributeIfUnset(SubscriberFields::FIRST_NAME, $user->getForename());
            $subscriber->setAttributeIfUnset(SubscriberFields::LAST_NAME, $user->getSurname());
        }
    }
}
