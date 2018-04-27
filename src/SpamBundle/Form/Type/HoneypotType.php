<?php

namespace Perform\SpamBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormError;
use Perform\SpamBundle\Event\HoneypotEvent;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class HoneypotType extends AbstractType
{
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::POST_SUBMIT, function ($event) use ($options) {
            $form = $event->getForm();
            if (!$form->getData()) {
                return;
            }
            $this->dispatcher->dispatch(HoneypotEvent::CAUGHT, new HoneypotEvent($form));

            if ($options['prevent_submission']) {
                $form->addError(new FormError($options['error_message']));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
            'mapped' => false,
            'prevent_submission' => false,
            'error_message' => 'An error occurred.',
            'attr' => [
                'autocomplete' => 'off',
                'tabindex' => -1,
                'style' => 'position: fixed; left: -100%; top: -100%;',
            ],
        ]);
    }

    public function getParent()
    {
        return TextType::class;
    }
}
