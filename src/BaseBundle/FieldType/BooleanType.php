<?php

namespace Perform\BaseBundle\FieldType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BooleanType extends AbstractType
{
    public function listContext($entity, $field, array $options = [])
    {
        $labels = $options['value_labels'];

        return [
            'value' => $this->getPropertyAccessor()->getValue($entity, $field) ? $labels[0] : $labels[1],
        ];
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $labels = $options['value_labels'];
        $builder->add($field, ChoiceType::class, [
            'label' => $options['label'],
            'choices' => [
                $labels[0] => true,
                $labels[1] => false,
            ],
            'expanded' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'value_labels' => ['Yes', 'No'],
        ]);
        $resolver->setAllowedTypes('value_labels', 'array');
    }
}
