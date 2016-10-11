<?php

namespace Perform\EventsBundle\Twig\Extension;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * EventsExtension
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EventsExtension extends \Twig_Extension
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_upcoming_events', [$this, 'getUpcoming']),
            new \Twig_SimpleFunction('perform_past_events', [$this, 'getPast']),
        ];
    }

    public function getUpcoming($limit = 5)
    {
        return $this->entityManager
            ->getRepository('PerformEventsBundle:Event')
            ->findUpcoming($limit);
    }

    public function getPast($limit = 5)
    {
        return $this->entityManager
            ->getRepository('PerformEventsBundle:Event')
            ->findPast($limit);
    }

    public function getName()
    {
        return 'events';
    }
}
