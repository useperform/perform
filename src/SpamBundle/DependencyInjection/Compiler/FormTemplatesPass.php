<?php

namespace Perform\SpamBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Add the templates for the spam form types automatically.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FormTemplatesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $templates = $container->getParameter('twig.form.resources');

        $honeypotTemplate = '@PerformSpam/form/honeypot.html.twig';

        if (!in_array($honeypotTemplate, $templates)) {
            array_unshift($templates, $honeypotTemplate);

            $container->setParameter('twig.form.resources', $templates);
        }
    }
}
