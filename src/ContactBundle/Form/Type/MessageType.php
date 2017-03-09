<?php

namespace Perform\ContactBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * MessageType
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('email')
            ->add('message', TextareaType::class, [
                'attr' => [
                    'rows' => 12,
                ],
            ]);

        $honeypotField = isset($options['honeypot_field']) ? $options['honeypot_field'] : 'rating';
        $builder->add($honeypotField, HoneypotType::class, [
            'prevent_submission' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Perform\ContactBundle\Entity\Message',
            'honeypot_field' => 'rating',
        ]);
    }
}
