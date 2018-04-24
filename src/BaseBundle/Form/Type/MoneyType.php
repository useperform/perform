<?php

namespace Perform\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Perform\BaseBundle\Form\DataTransformer\MoneyToArrayTransformer;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MoneyType extends AbstractType
{
    protected static $currencyCssClasses = [
        'GBP' => 'fa-gbp',
        'EUR' => 'fa-eur',
        'USD' => 'fa-usd',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', TextType::class)
            ->add('currency', HiddenType::class, [
                'empty_data' => $options['default_currency'],
            ])
            ->addModelTransformer(new MoneyToArrayTransformer(
                $options['currencies'],
                $options['default_currency']
            ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['default_currency'] = $options['default_currency'];
        // don't count this as a nested form to avoid <legend> and
        // <fieldset> in the markup
        $view->vars['compound'] = false;
        $view->vars['currency_classes'] = self::$currencyCssClasses;

        // give the amount box
        if (!$view->vars['valid']) {
            $view->children['currency']->vars['valid'] = false;
            $view->children['amount']->vars['valid'] = false;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'error_bubbling' => false,
                'currencies' => ['GBP'],
                'default_currency' => function(Options $options) {
                    if (!empty($options['currencies'])) {
                        return $options['currencies'][0];
                    }

                    return '';
                },
            ))
            ->setAllowedTypes('currencies', 'array')
            ->setAllowedTypes('default_currency', 'string')
            ->setNormalizer('default_currency', function(Options $options, $value) {
                if (!in_array($value, $options['currencies'], true)) {
                    throw new InvalidArgumentException(sprintf('The default_currency "%s" must be present in the currencies list ("%s").', $value, implode($options['currencies'], '", "')));
                }

                return $value;
            });
    }

    public function getBlockPrefix()
    {
        return 'perform_money';
    }
}
