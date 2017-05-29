<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType as FormType;

/**
 * Use the ``string`` type for simple strings.
 *
 * It should be used with doctrine ``string`` fields.
 * Forms will render a simple text input.
 *
 * @example
 * $config->add('title', [
 *     'type' => 'string',
 * ]);
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class StringType extends AbstractType
{
    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, FormType::class);
    }
}
