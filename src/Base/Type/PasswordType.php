<?php

namespace Admin\Base\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType as PasswordFormType;

/**
 * PasswordType
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PasswordType extends AbstractType
{
    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, PasswordFormType::class);
    }

    public function listContext($entity, $field, array $options = [])
    {
        return '[hidden]';
    }

}
