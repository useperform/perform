<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * EmailType
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EmailType extends AbstractType
{
    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, TextType::class);
    }

    public function listContext($entity, $field, array $options = [])
    {
        return [
            'email' => $this->accessor->getValue($entity, $field),
            'link' => (bool) $options['link'],
        ];
    }

    public function getDefaultConfig()
    {
        return [
            'options' => [
                'link' => true,
            ],
        ];
    }

    public function getTemplate()
    {
        return 'PerformBaseBundle:types:email.html.twig';
    }
}
