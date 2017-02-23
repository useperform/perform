<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Perform\BaseBundle\Type\TypeConfig;

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

    public function getTemplate()
    {
        return 'PerformBaseBundle:types:text.html.twig';
    }
}
