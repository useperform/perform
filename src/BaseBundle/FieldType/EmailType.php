<?php

namespace Perform\BaseBundle\FieldType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType as EmailFormType;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EmailType extends AbstractType
{
    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $formOptions = [
            'label' => $options['label'],
        ];
        $builder->add($field, EmailFormType::class, array_merge($formOptions, $options['form_options']));
    }

    public function listContext($entity, $field, array $options = [])
    {
        return [
            'email' => $this->getPropertyAccessor()->getValue($entity, $field),
            'link' => $options['link'],
        ];
    }

    public function exportContext($entity, $field, array $options = [])
    {
        return $this->getPropertyAccessor()->getValue($entity, $field);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('link', true)
            ->setAllowedTypes('link', 'boolean');
    }

    public function getDefaultConfig()
    {
        return [
            'template' => '@PerformBase/field_type/email.html.twig',
        ];
    }
}
