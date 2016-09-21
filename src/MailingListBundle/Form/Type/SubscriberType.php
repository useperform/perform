<?php

namespace Perform\MailingListBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * SubscriberType
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SubscriberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('forename', null, [
                'label' => 'First Name'
            ])
            ->add('surname', null, [
                'label' => 'Last Name'
            ])
            ->add('email')
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Perform\MailingListBundle\Entity\Subscriber',
        ]);
    }
}
