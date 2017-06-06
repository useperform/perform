<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * AbstractType
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
abstract class AbstractType implements TypeInterface
{
    protected $accessor;

    public function __construct()
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    public function listContext($entity, $field, array $options = [])
    {
        return $this->accessor->getValue($entity, $field);
    }

    public function viewContext($entity, $field, array $options = [])
    {
        return $this->listContext($entity, $field, $options);
    }

    public function editContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        return $this->createContext($builder, $field, $options);
    }

    public function getTemplate()
    {
        return 'PerformBaseBundle:types:simple.html.twig';
    }

    public function getDefaultConfig()
    {
        return [];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
