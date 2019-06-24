<?php

namespace Perform\BaseBundle\FieldType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
abstract class AbstractType implements FieldTypeInterface
{
    private static $propertyAccessor;

    protected function getPropertyAccessor()
    {
        if (!self::$propertyAccessor) {
            self::$propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        return self::$propertyAccessor;
    }

    public function listContext($entity, $field, array $options = [])
    {
        return [];
    }

    public function viewContext($entity, $field, array $options = [])
    {
        return $this->listContext($entity, $field, $options);
    }

    public function exportContext($entity, $field, array $options = [])
    {
        return $this->listContext($entity, $field, $options);
    }

    public function editContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        return $this->createContext($builder, $field, $options);
    }

    public function getDefaultConfig()
    {
        return [];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
