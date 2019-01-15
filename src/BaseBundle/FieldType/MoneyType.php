<?php

namespace Perform\BaseBundle\FieldType;

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
        $formOptions = [
            'label' => $options['label'],
        ];
        $builder->add($field, FormType::class, array_merge($formOptions, $options['form_options']));
    }

    public function getDefaultConfig()
    {
        return [
            'sort' => false,
            'template' => '@PerformBase/field_type/money.html.twig',
        ];
    }
}
