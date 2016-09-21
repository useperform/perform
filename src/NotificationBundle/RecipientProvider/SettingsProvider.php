<?php

namespace Perform\NotificationBundle\RecipientProvider;

use Doctrine\ORM\EntityManagerInterface;
use Perform\Base\Settings\SettingsManager;

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

    public function __construct(EntityManagerInterface $entityManager, SettingsManager $settings)
    {
        $this->entityManager = $entityManager;
        $this->settings = $settings;
    }

    public function getRecipients(array $criteria = [])
    {
        if (!isset($criteria['setting'])) {
            throw new \Exception(__CLASS__.' requires the "setting" criteria.');
        }

        //fetch emails using the settings store
        $emails = (array) $this->settings->getValue($criteria['setting']);
        $users = $this->entityManager->getRepository('PerformBaseBundle:User')
            ->findByEmails($emails);

        //if some emails are missing, create them with default names

        return $users;
    }
}
