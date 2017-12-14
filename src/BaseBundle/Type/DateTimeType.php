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
     * @doc datepicker_format The ICU format to use in the datepicker field.
     *
     * This is not the same as PHP's ``date()`` format.
     * See http://userguide.icu-project.org/formatparse/datetime and
     * http://www.unicode.org/reports/tr35/tr35-dates.html#Date_Field_Symbol_Table
     * for more information.
     *
     * @doc datepicker_pick_date If true, show the date component of the datepicker.
     *
     * @doc datepicker_pick_time If true, show the time component of the datepicker.
     *
     * @doc datepicker_week_start An integer between 0 and 6 declaring
     * which day the week starts on. Like javascript's date handling,
     * 0 is Sunday, 1 is Monday, etc.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'format' => 'g:ia d/m/Y',
            'human' => true,
            'datepicker' => true,
            'datepicker_format' => 'hh:mma dd/MM/yyyy',
            'datepicker_pick_date' => true,
            'datepicker_pick_time' => true,
            'datepicker_week_start' => 1,
        ]);
        $resolver->setRequired(['human', 'format']);
        $resolver->setAllowedTypes('human', 'boolean');
        $resolver->setAllowedTypes('format', 'string');
        $resolver->setAllowedTypes('datepicker', 'boolean');
        $resolver->setAllowedTypes('datepicker_format', 'string');
        $resolver->setAllowedTypes('datepicker_pick_date', 'boolean');
        $resolver->setAllowedTypes('datepicker_pick_time', 'boolean');
        $resolver->setAllowedTypes('datepicker_week_start', 'integer');
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

        $builder->add($field, DatePickerType::class, [
            'format' => $options['datepicker_format'],
            'datepicker_format' => $options['datepicker_format'],
            'pick_date' => $options['datepicker_pick_date'],
            'pick_time' => $options['datepicker_pick_time'],
            'week_start' => $options['datepicker_week_start'],
        ]);
    }
}
