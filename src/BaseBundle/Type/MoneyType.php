<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Perform\BaseBundle\Form\Type\MoneyType as FormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Use the ``money`` type for instances of moneyphp/money objects.
 *
 * @example
 * $config->add('amount', [
 *     'type' => 'money',
 *     'options' => [
 *         'form_options' => [
 *             'default_currency' => 'GBP',
 *             'currencies' => ['GBP', 'EUR', 'USD'],
 *         ]
 *     ]
 * ]);
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MoneyType extends AbstractType
{
    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, FormType::class, $options['form_options']);
    }

    /**
     * @doc form_options An array of options to pass to the underlying form type.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('form_options', [])
            ->setAllowedTypes('form_options', 'array');
    }

    public function getDefaultConfig()
    {
        return [
            'sort' => false,
            'template' => '@PerformBase/type/money.html.twig',
        ];
    }
}
