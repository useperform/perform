<?php

namespace Perform\ContactBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Add the honeypot form template automatically.
 **/
class FormTemplatePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $templates = $container->getParameter('twig.form.resources');

        $honeypotTemplate = '@PerformContact/form/honeypot.html.twig';

        if (!in_array($honeypotTemplate, $templates)) {
            array_unshift($templates, $honeypotTemplate);

            $container->setParameter('twig.form.resources', $templates);
        }
    }
}
