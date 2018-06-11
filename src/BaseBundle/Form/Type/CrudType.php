<?php

namespace Perform\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Perform\BaseBundle\FieldType\FieldTypeRegistry;
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
    protected $typeRegistry;

    public function __construct(ConfigStoreInterface $store, FieldTypeRegistry $typeRegistry)
    {
        $this->store = $store;
        $this->typeRegistry = $typeRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $context = $options['context'];
        $fields = $this->getTypes($options['crud_name'], $context);
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
        $view->vars['fields'] = $this->getTypes($options['crud_name'], $options['context']);
    }

    protected function getTypes($crudName, $context)
    {
        $typeConfig = $this->store->getFieldConfig($crudName);

        return $typeConfig->getTypes($context);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['context', 'crud_name']);
        $resolver->setAllowedValues('context', [CrudRequest::CONTEXT_CREATE, CrudRequest::CONTEXT_EDIT]);
        $resolver->setDefault('data_class', function (Options $options) {
            return $this->store->getEntityClass($options['crud_name']);
        });
    }
}
