<?php

namespace Perform\BaseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Add twig templates for extra form types.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FormTemplatesPass implements CompilerPassInterface
{
    const PARAM = 'perform_base.form_templates';

    public static function addTemplate(ContainerBuilder $container, $template)
    {
        $existing = $container->hasParameter(self::PARAM) ? $container->getParameter(self::PARAM) : [];
        $container->setParameter(self::PARAM, array_merge($existing, [$template]));
    }

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter(self::PARAM)) {
            return;
        }
        $templates = $container->getParameter('twig.form.resources');
        $extraTemplates = $container->getParameter(self::PARAM);

        foreach ($extraTemplates as $template) {
            if (!in_array($template, $templates)) {
                array_unshift($templates, $template);
            }
        }

        $container->setParameter('twig.form.resources', $templates);
        $container->getParameterBag()->remove(self::PARAM);
    }
}
