<?php

namespace Perform\BaseBundle\FieldType;

use Symfony\Component\Form\FormBuilderInterface;
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
     *
     * @doc view_timezone The timezone to use when displaying the data.
     * This option will be passed to the form type in the edit and create contexts.
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
            ],
            'view_timezone' => 'UTC',
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
            'template' => '@PerformBase/field_type/datetime.html.twig',
            'viewOptions' => [
                'human' => false,
            ],
        ];
    }

    public function listContext($entity, $field, array $options = [])
    {
        $datetime = $this->getPropertyAccessor()->getValue($entity, $field);
        if (!$datetime instanceof \DateTimeInterface || $datetime->format('Y') === '-0001') {
            $datetime = null;
        }

        return [
            'value' => $datetime,
            'field' => $field,
            'options' => $options,
        ];
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        if (!$options['datepicker']) {
            // select boxes
            $builder->add($field, FormType::class, [
                'view_timezone' => $options['view_timezone'],
            ]);

            return;
        }

        $builder->add($field, DatePickerType::class, $options['datepicker_options']);
    }
}
