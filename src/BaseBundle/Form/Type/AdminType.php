<?php

namespace Perform\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Perform\BaseBundle\Type\TypeRegistry;
use Perform\BaseBundle\Type\TypeConfig;

/**
 * AdminType
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $typeRegistry = $options['typeRegistry'];
        $fields = $options['typeConfig']->getTypes($options['context']);
        $method = $options['context'] === TypeConfig::CONTEXT_CREATE ? 'createContext' : 'editContext';

        foreach ($fields as $field => $config) {
            $type = $typeRegistry->getType($config['type']);
            $type->$method($builder, $field, $config['options']);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['context', 'typeConfig', 'typeRegistry']);
        $resolver->setAllowedValues('context', [TypeConfig::CONTEXT_CREATE, TypeConfig::CONTEXT_EDIT]);
        $resolver->setAllowedValues('typeConfig', function($admin) {
            return $admin instanceof TypeConfig;
        });
        $resolver->setAllowedValues('typeRegistry', function($typeRegistry) {
            return $typeRegistry instanceof TypeRegistry;
        });
    }
}
