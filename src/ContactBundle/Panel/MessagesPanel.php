<?php

namespace Admin\ContactBundle\Panel;

use Admin\Base\Panel\PanelInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * MessagesPanel
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MessagesPanel implements PanelInterface
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
        $messages = $this->entityManager->getRepository('AdminContactBundle:Message')
                  ->findBy([], ['createdAt' => 'DESC'], 10);

        return $this->templating->render('AdminContactBundle:panels:messages.html.twig', [
            'messages' => $messages,
        ]);
    }
}
