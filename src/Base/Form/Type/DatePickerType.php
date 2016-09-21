<?php

namespace Perform\Base\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * DatePickerType
 **/
class DatePickerType extends AbstractType
{
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
