<?php

namespace Perform\ContactBundle\Panel;

use Perform\BaseBundle\Panel\PanelInterface;
use Doctrine\ORM\EntityManagerInterface;
use Perform\BaseBundle\Doctrine\EntityResolver;
use Perform\ContactBundle\Entity\Message;
use Twig\Environment;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MessagesPanel implements PanelInterface
{
    protected $resolver;
    protected $entityManager;
    protected $twig;

    public function __construct(EntityResolver $resolver, EntityManagerInterface $entityManager, Environment $twig)
    {
        $this->resolver = $resolver;
        $this->entityManager = $entityManager;
        $this->twig = $twig;
    }

    public function render()
    {
        $messages = $this->entityManager->getRepository($this->resolver->resolve('PerformContactBundle:Message'))
                  ->findBy(['status' => Message::STATUS_NEW], ['createdAt' => 'DESC'], 10);

        return $this->twig->render('@PerformContact/panel/messages.html.twig', [
            'messages' => $messages,
        ]);
    }
}
