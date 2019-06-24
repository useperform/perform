<?php

namespace Perform\BaseBundle\FieldType;

use Perform\BaseBundle\Exception\InvalidFieldException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Perform\BaseBundle\Crud\CrudRequest;

/**
 * A simple read-only field type to display markup using a custom
 * template.
 * The given entity and field name will be passed to the template as
 * the ``entity`` and ``field`` twig variables.
 *
 * You must supply a template when using this type, otherwise nothing
 * will be rendered.
 *
 * @example
 * $config->add('some_property', [
 *     'type' => 'display',
 *     'template' => 'field_type/some_property.html.twig',
 * ]);
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DisplayType implements FieldTypeInterface
{
    public function listContext($entity, $field, array $options = [])
    {
        return [
            'entity' => $entity,
            'field' => $field,
        ];
    }

    public function viewContext($entity, $field, array $options = [])
    {
        return $this->listContext($entity, $field, $options);
    }

    public function exportContext($entity, $field, array $options = [])
    {
        return $this->listContext($entity, $field, $options);
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        throw new InvalidFieldException(sprintf('%s may only be used for list, view, and export contexts.', __CLASS__));
    }

    public function editContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        return $this->createContext($builder, $field, $options);
    }

    public function getDefaultConfig()
    {
        return [
            'template' => '@PerformBase/field_type/blank.html.twig',
            'contexts' => [
                CrudRequest::CONTEXT_LIST,
                CrudRequest::CONTEXT_VIEW,
            ],
            'sort' => false,
        ];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
