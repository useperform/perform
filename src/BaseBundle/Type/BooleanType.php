<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * BooleanType.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BooleanType extends AbstractType
{
    public function listContext($entity, $field, array $options = [])
    {
        $labels = $this->getLabels($options);

        return $this->accessor->getValue($entity, $field) ? $labels[0] : $labels[1];
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $labels = $this->getLabels($options);
        $builder->add($field, ChoiceType::class, [
            'label' => $options['label'],
            'choices' => [
                $labels[0] => true,
                $labels[1] => false,
            ],
            'expanded' => true,
        ]);
    }

    protected function getLabels(array $options)
    {
        $labels = $options['valueLabels'];
        if (count($labels) !== 2) {
            throw new \InvalidArgumentException(sprintf('%s expects the "valueLabels" option to be an array with 2 values.', __CLASS__));
        }

        return $labels;
    }

    public function getDefaultConfig()
    {
        return [
            'options' => [
                'valueLabels' => ['Yes', 'No'],
            ],
        ];
    }
}
