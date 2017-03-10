<?php

namespace Perform\BaseBundle\Twig\Extension;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Perform\BaseBundle\Action\ActionRegistry;

/**
 * ActionExtension.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionExtension extends \Twig_Extension
{
    protected $urlGenerator;
    protected $registry;

    public function __construct(UrlGeneratorInterface $urlGenerator, ActionRegistry $registry)
    {
        $this->urlGenerator = $urlGenerator;
        $this->registry = $registry;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_action_button', [$this, 'actionButton'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new \Twig_SimpleFunction('perform_action_is_granted', [$this, 'isActionGranted']),
            new \Twig_SimpleFunction('perform_action_for_entity', [$this->registry, 'getActionsForEntity']),
            new \Twig_SimpleFunction('perform_actions_for_class', [$this->registry, 'getActionsForEntityClass']),
        ];
    }

    public function actionButton(\Twig_Environment $twig, $actionName, $label, $entity, array $attr = [])
    {
        $action = json_encode([
            'action' => $actionName,
            'id' => $entity->getId(),
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

    public function isActionGranted($name, $entity)
    {
        return $this->registry->getAction($name)->isGranted($entity);
    }

    public function getName()
    {
        return 'perform_action';
    }
}
