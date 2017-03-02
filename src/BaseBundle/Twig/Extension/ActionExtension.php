<?php

namespace Perform\BaseBundle\Twig\Extension;

/**
 * ActionExtension.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionExtension extends \Twig_Extension
{
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
