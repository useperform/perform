<?php

namespace Perform\MailingListBundle\Twig\Extension;

use Perform\MailingListBundle\Form\UniqueFormFactory;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FormExtension extends \Twig_Extension
{
    protected $factory;

    public function __construct(UniqueFormFactory $factory)
    {
        $this->factory = $factory;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_mailing_list_form', [$this, 'getFormView']),
        ];
    }

    public function getFormView($name, $action)
    {
        return $this->factory->create($name, $action)->createView();
    }

    public function getName()
    {
        return 'mailing_list_form';
    }
}
