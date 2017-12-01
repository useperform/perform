<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Carbon\Carbon;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType as FormType;

/**
 * Use the ``datetime`` type for ``datetime`` doctrine fields.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DateTimeType extends AbstractType
{
    /**
     * @doc human Show the data as a human-friendly string, e.g. 10 minutes ago.
     * @doc format How to format the date, using PHP date() syntax.
     */
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
        $builder->add($field, FormType::class, [
            'format' => 'HH:mm dd/MM/y',
            'widget' => 'single_text',
            'html5' => true,
        ]);
    }
}
