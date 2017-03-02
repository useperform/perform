<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * BooleanType
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BooleanType extends AbstractType
{
    public function listContext($entity, $field, array $options = [])
    {
        $labels = $options['valueLabels'];
        if (count($labels) !== 2) {
            throw new \InvalidArgumentException(sprintf('%s expects the "valueLabels" option to be an array with 2 values.', __CLASS__));
        }

        return $this->accessor->getValue($entity, $field) ? $labels[0] : $labels[1];
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, CheckboxType::class);
    }

    public function getDefaultConfig()
    {
        return [
            'options' => [
                'valueLabels' => ['Yes', 'No'],
            ]
        ];
    }
}
