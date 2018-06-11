<?php

namespace Perform\BaseBundle\FieldType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType as ChoiceFormType;

/**
 * Use the ``choice`` type for fields whose value must be a member of a list.
 *
 * @example
 * $config->add('status', [
 *     'type' => 'choice',
 *     'options' => [
 *         'choices' => [
 *             'Started' => 1,
 *             'In Progress' => 2,
 *             'Done' => 3,
 *         ],
 *     ],
 *     'sort' => false,
 * ]);
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ChoiceType extends AbstractType
{
    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, ChoiceFormType::class, [
            'choices' => $options['choices'],
        ]);
    }

    public function listContext($entity, $field, array $options = [])
    {
        $val = $this->accessor->getValue($entity, $field);

        if (!$options['show_label']) {
            return $val;
        }

        if (!in_array($val, $options['choices'])) {
            return $options['unknown_label'];
        }

        return array_search($val, $options['choices']);
    }

    /**
     * @doc choices An array of choices, where the keys are the labels, and values the possible value.
     * @doc show_label If true, display the choice label instead of the value in ``list`` and ``view`` contexts.
     * @doc unknown_label The value to show when show_label is true and the value is not in the list of choices.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('choices');
        $resolver->setAllowedTypes('choices', 'array');
        $resolver->setDefault('show_label', true);
        $resolver->setAllowedTypes('show_label', 'boolean');
        $resolver->setDefault('unknown_label', '');
        $resolver->setAllowedTypes('unknown_label', 'string');
    }
}
