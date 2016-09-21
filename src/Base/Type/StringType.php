<?php

namespace Perform\Base\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * StringType
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class StringType extends AbstractType
{
    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, TextType::class);
    }
}
