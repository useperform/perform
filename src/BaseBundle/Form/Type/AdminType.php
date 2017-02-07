<?php

namespace Perform\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Perform\BaseBundle\Type\TypeRegistry;
use Perform\BaseBundle\Type\TypeConfig;
use Perform\BaseBundle\Type\EntityTypeConfig;
use Symfony\Component\OptionsResolver\Options;
use Perform\BaseBundle\Admin\AdminRegistry;

/**
 * AdminType.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AdminType extends AbstractType
{
    protected $adminRegistry;
    protected $typeRegistry;

    public function __construct(AdminRegistry $adminRegistry, TypeRegistry $typeRegistry)
    {
        $this->adminRegistry = $adminRegistry;
        $this->typeRegistry = $typeRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $typeConfig = $this->adminRegistry->getTypeConfig($options['entity']);
        $fields = $typeConfig->getTypes($options['context']);
        $method = $options['context'] === TypeConfig::CONTEXT_CREATE ? 'createContext' : 'editContext';

        foreach ($fields as $field => $config) {
            $type = $this->typeRegistry->getType($config['type']);
            $type->$method($builder, $field, $config['options']);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['context', 'entity']);
        $resolver->setAllowedValues('context', [TypeConfig::CONTEXT_CREATE, TypeConfig::CONTEXT_EDIT]);
        $resolver->setDefault('data_class', function(Options $options) {
            return $this->adminRegistry->resolveEntityAlias($options['entity']);
        });
    }
}
