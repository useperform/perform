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
    protected $forms = [];
    protected $instances = [];
    protected $factory;

    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_mailing_list_form', [$this, 'getForm']),
        ];
    }

    public function addForm($name, $type)
    {
        $this->forms[$name] = $type;
    }

    public function getForm($name, $action)
    {
        if (!isset($this->instances[$name])) {
            if (!isset($this->forms[$name])) {
                throw new \Exception(sprintf('Unknown mailing list form "%s"', $name));
            }
            $type = $this->forms[$name];

            $this->instances[$name] = $this->factory->create($type, null, [
                'action' => $action,
            ])->createView();
        }

        return $this->instances[$name];
    }

    public function getName()
    {
        return 'mailing_list_form';
    }
}
