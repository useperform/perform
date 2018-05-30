<?php

namespace Perform\BaseBundle\Twig\Extension;

use Perform\BaseBundle\Config\ConfigStoreInterface;
use Perform\BaseBundle\Routing\MissingResourceException;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Render export links.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ExportExtension extends \Twig_Extension
{
    protected $urlGenerator;
    protected $configStore;
    protected $request;

    public function __construct(UrlGeneratorInterface $urlGenerator, ConfigStoreInterface $configStore)
    {
        $this->urlGenerator = $urlGenerator;
        $this->configStore = $configStore;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_export_dropdown', [$this, 'exportDropdown'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new \Twig_SimpleFunction('perform_export_route', [$this, 'exportRoute']),
        ];
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->request = $event->getRequest();
    }

    public function exportDropdown(\Twig_Environment $twig, $crudName, $label = 'export.dropdown')
    {
        try {
            $this->urlGenerator->generate('perform_base_export_stream');
        } catch (RouteNotFoundException $e) {
            return '';
        }

        $formats = $this->configStore->getExportConfig($crudName)->getFormats();
        if (empty($formats)) {
            return '';
        }

        return $twig->render('@PerformBase/crud/_export_dropdown.html.twig', [
            'formats' => $formats,
            'crudName' => $crudName,
            'label' => $label,
        ]);
    }

    public function exportRoute($crudName, $format)
    {
        try {
            $vars = array_merge([
                'crud' => $crudName,
                'format' => $format,
            ], $this->request->query->all());
            // get all entities, not a single page
            // filters, sorting, etc are kept however
            unset($vars['page']);

            return $this->urlGenerator->generate('perform_base_export_stream', $vars);
        } catch (RouteNotFoundException $e) {
            throw MissingResourceException::create($e, '@PerformBaseBundle/Resources/config/routing_export.yml', 'to render export links', 'perform_base_export_stream');
        }
    }

    public function getName()
    {
        return 'perform_export';
    }
}
