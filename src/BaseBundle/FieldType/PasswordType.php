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
        $builder->add($field, FormType::class, [
            'label' => $options['label'],
        ]);
    }

    public function editContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, FormType::class, [
            'label' => $options['label'],
            'required' => false,
        ]);
    }

    public function listContext($entity, $field, array $options = [])
    {
        return [
            'value' => '********',
        ];
    }
}
