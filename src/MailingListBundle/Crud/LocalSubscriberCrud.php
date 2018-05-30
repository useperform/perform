<?php

namespace Perform\MailingListBundle\Crud;

use Perform\BaseBundle\Crud\AbstractCrud;
use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Crud\CrudRequest;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LocalSubscriberCrud extends AbstractCrud
{
    public function configureTypes(TypeConfig $config)
    {
        $config
            ->add('firstName', [
                'type' => 'string',
                'contexts' => [
                    CrudRequest::CONTEXT_VIEW,
                    CrudRequest::CONTEXT_CREATE,
                    CrudRequest::CONTEXT_EDIT,
                ]
            ])
            ->add('email', [
                'type' => 'email',
            ])
            ->add('lists', [
                'type' => 'entity',
                'options' => [
                    'multiple' => true,
                    'class' => 'PerformMailingListBundle:LocalList',
                    'display_field' => 'name',
                ],
                'sort' => false,
            ])
            ->add('createdAt', [
                'type' => 'datetime',
                'contexts' => [
                    CrudRequest::CONTEXT_LIST,
                    CrudRequest::CONTEXT_VIEW,
                ],
                'options' => [
                    'label' => 'Sign-up date',
                ],
            ])
            ;
    }
}
