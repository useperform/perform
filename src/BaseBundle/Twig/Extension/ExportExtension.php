<?php

namespace Perform\BaseBundle\Twig\Extension;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Perform\BaseBundle\Action\ConfiguredAction;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Perform\BaseBundle\Doctrine\EntityResolver;

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

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function exportRoute($entity, $format)
    {
        try {
            return $this->urlGenerator->generate('perform_base_export_stream', [
                'entity' => $this->resolver->resolve($entity),
                'format' => $format,
            ]);
        } catch (RouteNotFoundException $e) {
            throw new \RuntimeException('You must include routing_export.yml from the PerformBaseBundle to render exporter links. The "perform_base_export_stream" route does not exist.', 1, $e);
        }
    }

    public function getName()
    {
        return 'perform_export';
    }
}
