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
    protected $defaultDatePickerOptions = [
        'format' => 'dd/MM/yyyy',
        'pick_time' => false,
    ];

    public function getDefaultConfig()
    {
        return [
            'template' => '@PerformBase/field_type/datetime.html.twig',
            'options' => [
                'format' => 'd/m/Y',
                'human' => false,
            ],
        ];
    }
}
