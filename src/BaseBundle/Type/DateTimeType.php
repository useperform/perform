<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Carbon\Carbon;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType as FormType;
use Perform\BaseBundle\Form\Type\DatePickerType;

/**
 * Use the ``datetime`` type for ``datetime`` doctrine fields.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DateTimeType extends AbstractType
{
    /**
     * @doc format The format to use when displaying the value, using PHP's ``date()`` syntax.
     *
     * See https://php.net/date for more information.
     *
     * @doc human Show the data as a human-friendly string, e.g. 10 minutes ago.
     *
     * @doc datepicker If true, use the interactive datepicker to set the value in forms.
     *
     * @doc datepicker_options An array of options to pass to the datepicker form type, if used.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'format' => 'g:ia d/m/Y',
            'human' => true,
            'datepicker' => true,
            'datepicker_options' => [
                'format' => 'hh:mma dd/MM/yyyy',
                'pick_date' => true,
                'pick_time' => true,
            ]
        ]);
        $resolver->setRequired(['human', 'format']);
        $resolver->setAllowedTypes('human', 'boolean');
        $resolver->setAllowedTypes('format', 'string');
        $resolver->setAllowedTypes('datepicker', 'boolean');
        $resolver->setAllowedTypes('datepicker_options', 'array');
    }

    public function getDefaultConfig()
    {
        return [
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
        if (!$options['datepicker']) {
            // select boxes
            $builder->add($field, FormType::class, [
            ]);

            return;
        }

        $builder->add($field, DatePickerType::class, $options['datepicker_options']);
    }
}
