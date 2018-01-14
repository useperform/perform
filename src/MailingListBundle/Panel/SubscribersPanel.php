<?php

namespace Perform\MailingListBundle\Panel;

use Perform\BaseBundle\Panel\PanelInterface;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SubscribersPanel implements PanelInterface
{
    protected $entityManager;
    protected $twig;

    public function __construct(EntityManagerInterface $entityManager, Environment $twig)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
    }

    public function render()
    {
        $subscribers = $this->entityManager->getRepository('PerformMailingListBundle:LocalSubscriber')
                  ->findBy([], ['createdAt' => 'DESC'], 10);

        return $this->twig->render('@PerformMailingList/panel/subscribers.html.twig', [
            'subscribers' => $subscribers,
        ]);
    }
}
