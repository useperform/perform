<?php

namespace Perform\BaseBundle\FieldType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType as IntegerFormType;

/**
 * IntegerType.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class IntegerType extends AbstractType
{
    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, IntegerFormType::class);
    }
}
