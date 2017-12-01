<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType as FormType;

/**
 * Use the ``date`` type for ``date`` doctrine fields.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DateType extends DateTimeType
{
    public function getDefaultConfig()
    {
        return [
            'options' => [
                'format' => 'd/m/Y',
                'human' => false,
            ],
        ];
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, FormType::class, [
            'format' => 'dd/MM/y',
            'widget' => 'single_text',
            'html5' => true,
        ]);
    }
}
