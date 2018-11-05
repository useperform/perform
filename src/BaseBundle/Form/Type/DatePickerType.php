<?php

namespace Perform\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Perform\BaseBundle\Asset\AssetContainer;

/**
 * Select dates, times, and timezones with an interactive picker.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DatePickerType extends AbstractType
{
    protected $assets;

    public function __construct(AssetContainer $assets)
    {
        $this->assets = $assets;
    }

    /**
     * @doc format The ICU format to use in the datepicker field.
     *
     * This is not the same as PHP's ``date()`` format.
     * See http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax
     * for more information.
     *
     * @doc pick_date If true, show the date component of the datepicker.
     *
     * @doc pick_time If true, show the time component of the datepicker.
     *
     * @doc week_start An integer between 0 and 6 declaring
     * which day the week starts on. Like javascript's date handling,
     * 0 is Sunday, 1 is Monday, etc.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'widget' => 'single_text',
            'html5' => false,
            'format' => 'MMM d, Y h:mma',
            'pick_date' => true,
            'pick_time' => false,
            'week_start' => 1,
        ]);
        $resolver->setAllowedTypes('pick_date', 'boolean');
        $resolver->setAllowedTypes('pick_time', 'boolean');
        $resolver->setAllowedTypes('week_start', 'integer');
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $this->assets->addJs('/bundles/performbase/js/types/datetime.js');
        $view->vars['flatPickrConfig'] = json_encode([]);
    }

    public function getParent()
    {
        return DateTimeType::class;
    }

    public function getBlockPrefix()
    {
        return 'datepicker';
    }
}
