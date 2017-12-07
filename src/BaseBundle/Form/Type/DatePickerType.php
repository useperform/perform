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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'widget' => 'single_text',
            //does not use date(), but intl constants
            //http://userguide.icu-project.org/formatparse/datetime
            'format' => 'dd/MM/y',
            //http://momentjs.com/docs/#/displaying/format/
            'datepicker_format' => 'DD/MM/YYYY',
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $this->assets->addJs('/bundles/performbase/js/types/datetime.js');
        $view->vars['datepicker_format'] = $options['datepicker_format'];
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
