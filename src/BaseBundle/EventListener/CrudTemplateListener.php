<?php

namespace Perform\BaseBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Perform\BaseBundle\Controller\CrudController;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * Allows crud controllers to optionally override crud templates.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class CrudTemplateListener
{
    protected $locator;

    // inject a service locator instead of dependencies for the sake of speed.
    // this listener will run every request, so don't build and inject
    // services just for them not to be used.
    public function __construct(ServiceLocator $locator)
    {
        $this->locator = $locator;
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
        if ($request->attributes->has('_template') || !$request->attributes->has('_crud')) {
            return;
        }

        $crudName = $request->attributes->get('_crud');

        //remove Action
        $context = substr($controller[1], 0, -6);

        $template = $this->locator->get('registry')
                  ->get($crudName)
                  ->getTemplate($this->locator->get('twig'), $crudName, $context);

        $annotation = new Template([]);
        $annotation->setTemplate($template);
        $annotation->setOwner($controller);
        $request->attributes->set('_template', $annotation);
    }
}
