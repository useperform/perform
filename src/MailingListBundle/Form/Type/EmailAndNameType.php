<?php

namespace Perform\MailingListBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Perform\MailingListBundle\Entity\Subscriber;
use Perform\MailingListBundle\SubscriberFields;
use Symfony\Component\Form\FormEvents;

/**
 * EmailAndNameType.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EmailAndNameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('firstName', TextType::class, [
                'mapped' => false,
            ])
            ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, function ($event) {
            $form = $event->getForm();
            $sub = $event->getData();

            $sub->setAttribute(SubscriberFields::FIRST_NAME, $form->get('firstName')->getData());
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Subscriber::class,
        ]);
    }
}
