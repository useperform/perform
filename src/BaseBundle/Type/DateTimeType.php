<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Carbon\Carbon;
use Perform\BaseBundle\Form\Type\DatePickerType;

/**
 * DateTimeType
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DateTimeType extends AbstractType
{
    protected $defaultOptions = [
        'format' => 'g:ia d/m/Y',
    ];

    public function listContext($entity, $field, array $options = [])
    {
        $datetime = $this->accessor->getValue($entity, $field);
        if (!$datetime instanceof \DateTime || $datetime->format('Y') === '-0001') {
            return 'Unknown';
        }

        $options = array_merge($this->defaultOptions, $options);
        //human by default in the listing
        if (!isset($options['human'])) {
            $options['human'] = true;
        }

        if ($options['human']) {
            return Carbon::instance($datetime)->diffForHumans();
        }

        return $datetime->format($options['format']);
    }

    public function viewContext($entity, $field, array $options = [])
    {
        //datetime format by default when viewing
        if (!isset($options['human'])) {
            $options['human'] = false;
        }

        return $this->listContext($entity, $field, $options);
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, DatePickerType::class, [
            'format' => 'h:mma dd/MM/y',
            'datepicker_format' => 'h:mmA DD/MM/YYYY',
        ]);
    }
}
