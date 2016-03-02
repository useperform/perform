<?php

namespace Admin\Base\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Admin\Base\Admin\AdminInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * AdminType
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $admin = $options['admin'];
        $fields = $options['context'] === 'create' ? $admin->getCreateFields() : $admin->getEditFields();
        foreach ($fields as $label => $field) {
            $builder->add($field);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['context', 'admin']);
        $resolver->setAllowedValues('context', ['create', 'edit']);
        $resolver->setAllowedValues('admin', function($admin) {
            return $admin instanceof AdminInterface;
        });
    }
}
