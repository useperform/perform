<?php

namespace Admin\ContactBundle\Panel;

use Admin\Base\Panel\PanelInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Doctrine\ORM\EntityManagerInterface;
use Admin\Base\Doctrine\EntityResolver;

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
        $messages = $this->entityManager->getRepository($this->resolver->resolve('AdminContactBundle:Message'))
                  ->findBy([], ['createdAt' => 'DESC'], 10);

        return $this->templating->render('AdminContactBundle:panels:messages.html.twig', [
            'messages' => $messages,
        ]);
    }
}
