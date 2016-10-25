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

    public function listContext($entity, $field, array $options = [])
    {
        //escape the content, then run it through nl2br
        return nl2br(htmlspecialchars($this->accessor->getValue($entity, $field)));
    }

    public function getHtmlContexts()
    {
        return [
            TypeConfig::CONTEXT_LIST,
            TypeConfig::CONTEXT_VIEW,
        ];
    }
}
