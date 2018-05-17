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

    // inject the container instead of dependencies for the sake of speed.
    // this listener will run every request, so don't build and inject
    // admin services just for them not to be used.
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
        $context = substr($controller[1], 0, -6);

        $template = $this->container->get('perform_base.crud.registry')
                  ->getCrud($entity)
                  ->getTemplate($this->container->get('twig'), $entity, $context);

        $annotation = new Template([]);
        $annotation->setTemplate($template);
        $annotation->setOwner($controller);
        $request->attributes->set('_template', $annotation);
    }
}
