<?php

namespace Perform\BaseBundle\FieldType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * BooleanType.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BooleanType extends AbstractType
{
    public function listContext($entity, $field, array $options = [])
    {
        $labels = $options['valueLabels'];

        return $this->accessor->getValue($entity, $field) ? $labels[0] : $labels[1];
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $labels = $options['valueLabels'];
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
            'valueLabels' => ['Yes', 'No'],
        ]);
        $resolver->setAllowedTypes('valueLabels', 'array');
    }
}
