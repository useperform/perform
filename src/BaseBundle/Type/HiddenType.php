<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType as FormType;

/**
 * HiddenType
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class HiddenType extends AbstractType
{
    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, FormType::class);
    }
}
