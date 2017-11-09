<?php

namespace Perform\BaseBundle\Twig\Extension;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Perform\BaseBundle\Doctrine\EntityResolver;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Render export links.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ExportExtension extends \Twig_Extension
{
    protected $urlGenerator;
    protected $resolver;
    protected $request;

    public function __construct(UrlGeneratorInterface $urlGenerator, EntityResolver $resolver)
    {
        $this->urlGenerator = $urlGenerator;
        $this->resolver = $resolver;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_export_route', [$this, 'exportRoute']),
        ];
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->request = $event->getRequest();
    }

    public function exportRoute($entity, $format)
    {
        try {
            $vars = array_merge([
                'entity' => $this->resolver->resolve($entity),
                'format' => $format,
            ], $this->request->query->all());
            // get all entities, not a single page
            // filters, sorting, etc are kept however
            unset($vars['page']);

            return $this->urlGenerator->generate('perform_base_export_stream', $vars);
        } catch (RouteNotFoundException $e) {
            throw new \RuntimeException('You must include routing_export.yml from the PerformBaseBundle to render exporter links. The "perform_base_export_stream" route does not exist.', 1, $e);
        }
    }

    public function getName()
    {
        return 'perform_export';
    }
}
