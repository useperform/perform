<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Carbon\Carbon;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType as FormType;
use Perform\BaseBundle\Asset\AssetContainer;
use Perform\BaseBundle\Form\Type\DatePickerType;

/**
 * Use the ``datetime`` type for ``datetime`` doctrine fields.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DateTimeType extends AbstractType
{
    protected $assets;

    public function __construct(AssetContainer $assets)
    {
        parent::__construct();
        $this->assets = $assets;
    }

    /**
     * @doc human Show the data as a human-friendly string, e.g. 10 minutes ago.
     * @doc format How to format the date, using PHP date() syntax.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'format' => 'g:ia d/m/Y',
            'human' => true,
            'datepicker' => true,
        ]);
        $resolver->setRequired(['human', 'format']);
        $resolver->setAllowedTypes('human', 'boolean');
        $resolver->setAllowedTypes('format', 'string');
        $resolver->setAllowedTypes('datepicker', 'boolean');
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
            $builder->add($field, FormType::class, [
                'format' => 'HH:mm dd/MM/y',
                'widget' => 'single_text',
                'html5' => true,
            ]);

            return;
        }

        // this could actually go in the datepicker form type, so this and datetype don't need to add it
        $this->assets->addJs('/bundles/performbase/js/types/datetime.js');
        $builder->add($field, DatePickerType::class, [
            'format' => 'h:mma dd/MM/y',
            'datepicker_format' => 'h:mmA DD/MM/YYYY',
        ]);
    }
}
