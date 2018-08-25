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

        return [];
    }

    public function listContext($entity, $field, array $options = [])
    {
        return [
            'entity' => $entity,
            'field' => $field,
            'multiple' => $options['multiple'],
            'show_label' => $options['show_label'],
            'choices' => $options['choices'],
            'choice_labels' => array_flip($options['choices']),
            'unknown_label' => $options['unknown_label'],
        ];
    }

    /**
     * @doc choices An array of choices, where the keys are the labels, and values the possible value.
     * @doc multiple If true, allow for an array of multiple values.
     * @doc show_label If true, display the choice label instead of the value in ``list`` and ``view`` contexts.
     * @doc unknown_label The value to show when show_label is true and the value is not in the list of choices.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('choices');
        $resolver->setAllowedTypes('choices', 'array');
        $resolver->setDefaults([
            'multiple' => false,
            'show_label' => true,
            'unknown_label' => '',
        ]);
        $resolver->setAllowedTypes('multiple', 'boolean');
        $resolver->setAllowedTypes('show_label', 'boolean');
        $resolver->setAllowedTypes('unknown_label', 'string');
    }

    public function getDefaultConfig()
    {
        return [
            'template' => '@PerformBase/field_type/choice.html.twig',
        ];
    }
}
