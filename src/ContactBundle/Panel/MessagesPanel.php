<?php

namespace Perform\ContactBundle\Panel;

use Perform\BaseBundle\Panel\PanelInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Doctrine\ORM\EntityManagerInterface;
use Perform\BaseBundle\Doctrine\EntityResolver;
use Perform\ContactBundle\Entity\Message;

/**
 * MessagesPanel
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MessagesPanel implements PanelInterface
{
    protected $resolver;
    protected $entityManager;
    protected $templating;

    public function __construct(EntityResolver $resolver, EntityManagerInterface $entityManager, EngineInterface $templating)
    {
        $this->resolver = $resolver;
        $this->entityManager = $entityManager;
        $this->templating = $templating;
    }

    public function render()
    {
        $messages = $this->entityManager->getRepository($this->resolver->resolve('PerformContactBundle:Message'))
                  ->findBy(['status' => Message::STATUS_NEW], ['createdAt' => 'DESC'], 10);

        return $this->templating->render('@PerformContact/panel/messages.html.twig', [
            'messages' => $messages,
        ]);
    }
}
