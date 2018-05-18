<?php

namespace Perform\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Perform\BaseBundle\Type\TypeRegistry;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Perform\BaseBundle\Doctrine\EntityResolver;
use Perform\BaseBundle\Config\ConfigStoreInterface;
use Perform\BaseBundle\Crud\CrudRequest;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudType extends AbstractType
{
    protected $store;
    protected $resolver;
    protected $typeRegistry;

    public function __construct(ConfigStoreInterface $store, EntityResolver $resolver, TypeRegistry $typeRegistry)
    {
        $this->store = $store;
        $this->resolver = $resolver;
        $this->typeRegistry = $typeRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $context = $options['context'];
        $fields = $this->getTypes($options['entity'], $context);
        $method = $context === CrudRequest::CONTEXT_CREATE ? 'createContext' : 'editContext';
        $templateVars = [];

        foreach ($fields as $field => $config) {
            $type = $this->typeRegistry->getType($config['type']);
            //record returned vars to push to the template later
            $templateVars[$field] = (array) $type->$method($builder, $field, $config[$context.'Options']);
        }

        $builder->setAttribute('type_vars', $templateVars);
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['type_vars'] = $form->getConfig()->getAttribute('type_vars');
        $view->vars['fields'] = $this->getTypes($options['entity'], $options['context']);
    }

    protected function getTypes($entity, $context)
    {
        $typeConfig = $this->store->getTypeConfig($entity);

        return $typeConfig->getTypes($context);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['context', 'entity']);
        $resolver->setAllowedValues('context', [CrudRequest::CONTEXT_CREATE, CrudRequest::CONTEXT_EDIT]);
        $resolver->setDefault('data_class', function (Options $options) {
            return $this->resolver->resolve($options['entity']);
        });
    }
}
