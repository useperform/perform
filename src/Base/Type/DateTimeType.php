<?php

namespace Admin\Base\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType as FormDateTimeType;
use Carbon\Carbon;

/**
 * DateTimeType
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DateTimeType extends AbstractType
{
    protected $defaultOptions = [
        'format' => 'Y/m/d H:i',
    ];

    public function getName()
    {
        return 'datetime';
    }

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
        $builder->add($field, FormDateTimeType::class);
    }
}
