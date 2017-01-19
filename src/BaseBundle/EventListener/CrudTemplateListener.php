<?php

namespace Perform\BaseBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Perform\BaseBundle\Controller\CrudController;

/**
 * Allows crud controllers to optionally override crud templates.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class CrudTemplateListener
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Chooses the correct template name to use if the controller is a crud controller action.
     *
     * @param FilterControllerEvent $event A FilterControllerEvent instance
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if (!is_array($controller = $event->getController())) {
            return;
        }

        if (!$controller[0] instanceof CrudController) {
            return;
        }

        $request = $event->getRequest();
        //don't override @Template annotation
        if ($request->attributes->has('_template') || !$request->attributes->has('_entity')) {
            return;
        }

        $entity = $request->attributes->get('_entity');

        //remove Action
        $segment = substr($controller[1], 0, -6);

        //try a template in the entity bundle first, e.g.
        //PerformContactBundle:Message:view.html.twig
        $template = $entity.':'.$segment.'.html.twig';
        $templating = $this->container->get('templating');

        if (!$templating->exists($template)) {
            $template = 'PerformBaseBundle:Crud:'.$segment.'.html.twig';
        }

        $annotation = new Template([]);
        $annotation->setTemplate($template);
        $annotation->setOwner($controller);
        $request->attributes->set('_template', $annotation);
    }
}
