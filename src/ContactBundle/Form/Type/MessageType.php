<?php

namespace Perform\ContactBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Perform\SpamBundle\Form\Type\HoneypotType;
use Perform\ContactBundle\Entity\Message;

/**
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
            ])
            ->add($options['honeypot_field'], HoneypotType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
            'honeypot_field' => 'rating',
        ]);
    }
}
