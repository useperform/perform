<?php

namespace Admin\NotificationBundle\RecipientProvider;

use Doctrine\ORM\EntityManagerInterface;

/**
 * SettingsProvider finds users whose email addresses are set in a given
 * setting.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SettingsProvider implements RecipientProviderInterface
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getRecipients(array $criteria = [])
    {
        if (!isset($criteria['setting'])) {
            throw new \Exception(__CLASS__.' requires the "setting" criteria.');
        }

        //fetch emails using the settings store
        $emails = ['me@glynnforrest.com'];

        return $this->entityManager->getRepository('AdminBaseBundle:User')
            ->findByEmails($emails);
    }
}
