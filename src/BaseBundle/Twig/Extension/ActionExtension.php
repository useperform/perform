<?php

namespace Perform\BaseBundle\Twig\Extension;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * ActionExtension.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionExtension extends \Twig_Extension
{
    protected $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_action_button', [$this, 'actionButton'], ['is_safe' => ['html'], 'needs_environment' => true]),
        ];
    }

    public function actionButton(\Twig_Environment $twig, $actionName, $label, $entity, array $attr = [])
    {
        $action = json_encode([
            'action' => $actionName,
            'entity' => $entity->getId(),
        ]);
        $attr['class'] = 'action-button' .
                       (isset($attr['class']) ? ' '.trim($attr['class']) : '');
        $attr['href'] = $this->urlGenerator->generate('perform_base_action_index', ['action' => $actionName]);

        return $twig->render('PerformBaseBundle:Action:button.html.twig', [
            'action' => $action,
            'attr' => $attr,
            'label' => $label,
        ]);
    }

    public function getName()
    {
        return 'perform_action';
    }
}
