<?php

namespace Perform\BaseBundle\Twig\Extension;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Perform\BaseBundle\Action\ActionRegistry;
use Perform\BaseBundle\Action\ConfiguredAction;

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

    public function actionButton(\Twig_Environment $twig, ConfiguredAction $action, $entity, array $attr = [])
    {
        $label = $action->getLabel($entity);

        $attr['data-action'] = json_encode([
            'entityClass' => get_class($entity),
            'id' => $entity->getId(),
            'label' => $label,
            'message' => $action->getConfirmationMessage($entity),
        ]);
        $actionClass = $action->isConfirmationRequired() ? 'action-confirm' : 'action-button';
        $attr['class'] = isset($attr['class']) ? $actionClass.' '.trim($attr['class']) : $actionClass;
        $attr['href'] = $this->urlGenerator->generate('perform_base_action_index', ['action' => $action->getName()]);

        return $twig->render('PerformBaseBundle:Action:button.html.twig', [
            'label' => $label,
            'attr' => $attr,
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
