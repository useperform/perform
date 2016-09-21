<?php

namespace Perform\BaseBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Perform\BaseBundle\Annotation\Ajax;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * AjaxListener.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AjaxListener implements EventSubscriberInterface
{
    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->attributes->get('_ajax') instanceof Ajax || $request->isXmlHttpRequest()) {
            return;
        }

        throw new AccessDeniedException('This route is only accessible with ajax.');
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->attributes->get('_ajax') instanceof Ajax) {
            return;
        }

        $content = $event->getControllerResult();
        if (is_array($content)) {
            $code = isset($content['code']) ? $content['code'] : JsonResponse::HTTP_OK;
            unset($content['code']);
            $event->setResponse(new JsonResponse($content, $code));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController', -128],
            KernelEvents::VIEW => ['onKernelView', -128],
        ];
    }
}
