<?php

namespace Admin\Base\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * TextType
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TextType extends AbstractType
{
    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, TextareaType::class);
    }
}
