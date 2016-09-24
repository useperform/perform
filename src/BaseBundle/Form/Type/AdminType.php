<?php

namespace Perform\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Perform\BaseBundle\Admin\AdminInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Perform\BaseBundle\Type\TypeRegistry;

/**
 * AdminType
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $admin = $options['admin'];
        $typeRegistry = $options['typeRegistry'];
        $fields = $options['context'] === 'create' ? $admin->getCreateFields() : $admin->getEditFields();
        $method = $options['context'] === 'create' ? 'createContext' : 'editContext';

        foreach ($fields as $field => $options) {
            $type = $typeRegistry->getType($options['type'], $options);
            $type->$method($builder, $field, $options);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['context', 'admin', 'typeRegistry']);
        $resolver->setAllowedValues('context', ['create', 'edit']);
        $resolver->setAllowedValues('admin', function($admin) {
            return $admin instanceof AdminInterface;
        });
        $resolver->setAllowedValues('typeRegistry', function($typeRegistry) {
            return $typeRegistry instanceof TypeRegistry;
        });
    }
}
