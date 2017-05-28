<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Carbon\Carbon;
use Perform\BaseBundle\Form\Type\DatePickerType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * DateTimeType
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DateTimeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['human', 'format']);
        $resolver->setAllowedTypes('human', 'boolean');
        $resolver->setAllowedTypes('format', 'string');
    }

    public function getDefaultConfig()
    {
        return [
            'options' => [
                'format' => 'g:ia d/m/Y',
                'human' => true,
            ],
            'viewOptions' => [
                'human' => false,
            ],
        ];
    }

    public function listContext($entity, $field, array $options = [])
    {
        $datetime = $this->accessor->getValue($entity, $field);
        if (!$datetime instanceof \DateTime || $datetime->format('Y') === '-0001') {
            return 'Unknown';
        }

        if ($options['human']) {
            return Carbon::instance($datetime)->diffForHumans();
        }

        return $datetime->format($options['format']);
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, DatePickerType::class, [
            'format' => 'h:mma dd/MM/y',
            'datepicker_format' => 'h:mmA DD/MM/YYYY',
        ]);
    }
}
