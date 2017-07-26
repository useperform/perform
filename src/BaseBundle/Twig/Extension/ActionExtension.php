<?php

namespace Perform\BaseBundle\Twig\Extension;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Perform\BaseBundle\Action\ActionRegistry;
use Perform\BaseBundle\Action\ConfiguredAction;
use Perform\BaseBundle\Admin\AdminRequest;
use Perform\BaseBundle\Config\ConfigStoreInterface;

/**
 * ActionExtension.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionExtension extends \Twig_Extension
{
    protected $urlGenerator;
    protected $registry;
    protected $store;
    protected $request;

    public function __construct(UrlGeneratorInterface $urlGenerator, ActionRegistry $registry, ConfigStoreInterface $store)
    {
        $this->urlGenerator = $urlGenerator;
        $this->registry = $registry;
        $this->store = $store;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_action_button', [$this, 'actionButton'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new \Twig_SimpleFunction('perform_action_option', [$this, 'actionOption'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new \Twig_SimpleFunction('perform_actions_for_entity', [$this, 'actionsForEntity']),
        ];
    }

    public function setAdminRequest(AdminRequest $request)
    {
        $this->request = $request;
    }

    public function actionButton(\Twig_Environment $twig, ConfiguredAction $action, $entity, $context, array $attr = [])
    {
        $label = $action->getLabel($this->request, $entity);

        $attr['data-action'] = json_encode([
            'entityClass' => get_class($entity),
            'ids' => [$entity->getId()],
            'label' => $label,
            'context' => $context,
            'message' => $action->getConfirmationMessage($this->request, $entity),
            'confirm' => $action->isConfirmationRequired(),
            'buttonStyle' => $action->getButtonStyle(),
        ]);
        $attr['class'] = sprintf('%s %s%s',
                                 'action-button btn',
                                 $action->getButtonStyle(),
                                 isset($attr['class']) ? ' '.trim($attr['class']) : '');
        $attr['href'] = $this->urlGenerator->generate('perform_base_action_index', ['action' => $action->getName()]);

        return $twig->render('PerformBaseBundle:Action:button.html.twig', [
            'label' => $label,
            'attr' => $attr,
        ]);
    }

    public function actionOption(\Twig_Environment $twig, ConfiguredAction $action, $entityClass, $context)
    {
        $label = $action->getBatchLabel($this->request);

        $attr = [];
        $attr['data-action'] = json_encode([
            'entityClass' => $entityClass,
            'label' => $label,
            'context' => $context,
            //need to change the message depending on the number of entities - ajax?
            'message' => 'Are you sure you want to do this?',
            'confirm' => $action->isConfirmationRequired(),
            'buttonStyle' => $action->getButtonStyle(),
        ]);
        $attr['value'] = $this->urlGenerator->generate('perform_base_action_index', ['action' => $action->getName()]);

        return $twig->render('PerformBaseBundle:Action:option.html.twig', [
            'attr' => $attr,
            'label' => $label,
        ]);
    }

    public function actionsForEntity($entity)
    {
        return $this->store->getActionConfig($entity)->forEntity($entity);
    }

    public function getName()
    {
        return 'perform_action';
    }
}
