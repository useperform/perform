<?php

namespace Perform\Base\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Perform\Base\Form\Type\DatePickerType;

/**
 * DateType.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DateType extends DateTimeType
{
    protected $defaultOptions = [
        'format' => 'd/m/Y',
        'human' => false,
    ];

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, DatePickerType::class, [
            'format' => 'dd/MM/y',
            'datepicker_format' => 'DD/MM/YYYY',
        ]);
    }
}
