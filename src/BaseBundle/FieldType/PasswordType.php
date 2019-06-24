<?php

namespace Perform\BaseBundle\FieldType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType as FormType;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PasswordType extends AbstractType
{
    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $formOptions = [
            'label' => $options['label'],
        ];
        $builder->add($field, FormType::class, array_merge($formOptions, $options['form_options']));
    }

    public function editContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $formOptions = [
            'label' => $options['label'],
            'required' => false,
        ];
        $builder->add($field, FormType::class, array_merge($formOptions, $options['form_options']));
    }

    public function listContext($entity, $field, array $options = [])
    {
        return [
            'value' => '********',
        ];
    }
}
