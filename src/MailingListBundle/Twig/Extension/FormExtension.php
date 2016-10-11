<?php

namespace Perform\MailingListBundle\Twig\Extension;

use Symfony\Component\Form\FormFactoryInterface;
use Perform\MailingListBundle\Form\Type\SubscriberType;

/**
 * FormExtension
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FormExtension extends \Twig_Extension
{
    protected $form;
    protected $factory;

    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_subscriber_form', [$this, 'getForm']),
        ];
    }

    public function getForm($action)
    {
        if (!$this->form) {
            $this->form = $this->factory->create(SubscriberType::class, null, [
                'action' => $action,
            ])->createView();
        }

        return $this->form;
    }

    public function getName()
    {
        return 'subscriberForm';
    }
}
