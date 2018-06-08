<?php

namespace Perform\BaseBundle\FieldType;

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
                'datepicker_options' => [
                    'format' => 'dd/MM/yyyy',
                    'pick_time' => false,
                ]
            ],
        ];
    }
}
