<?php

namespace Perform\MailingListBundle\Panel;

use Perform\Base\Panel\PanelInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * SubscribersPanel
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SubscribersPanel implements PanelInterface
{
    protected $entityManager;
    protected $templating;

    public function __construct(EntityManagerInterface $entityManager, EngineInterface $templating)
    {
        $this->entityManager = $entityManager;
        $this->templating = $templating;
    }

    public function render()
    {
        $subscribers = $this->entityManager->getRepository('PerformMailingListBundle:Subscriber')
                  ->findBy([], ['createdAt' => 'DESC'], 10);

        return $this->templating->render('PerformMailingListBundle:panels:subscribers.html.twig', [
            'subscribers' => $subscribers,
        ]);
    }
}
