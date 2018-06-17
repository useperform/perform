<?php

namespace Perform\NotificationBundle\RecipientProvider;

use Doctrine\ORM\EntityManagerInterface;
use Perform\BaseBundle\Settings\Manager\SettingsManagerInterface;
use Perform\NotificationBundle\Recipient\SimpleRecipient;

/**
 * SettingsProvider finds users whose email addresses are set in a given
 * setting.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SettingsProvider implements RecipientProviderInterface
{
    protected $entityManager;
    protected $settings;

    public function __construct(EntityManagerInterface $entityManager, SettingsManagerInterface $settings)
    {
        $this->entityManager = $entityManager;
        $this->settings = $settings;
    }

    public function getRecipients(array $criteria = [])
    {
        if (!isset($criteria['setting'])) {
            throw new \Exception(__CLASS__.' requires the "setting" criteria.');
        }

        $emails = (array) $this->settings->getValue($criteria['setting']);
        $users = $this->entityManager->getRepository('PerformUserBundle:User')
            ->findByEmails($emails);

        $missingEmails = array_combine($emails, $emails);
        foreach ($users as $user) {
            unset($missingEmails[$user->getEmail()]);
        }

        foreach ($missingEmails as $email) {
            $users[] = new SimpleRecipient($email, $email);
        }

        return $users;
    }
}
