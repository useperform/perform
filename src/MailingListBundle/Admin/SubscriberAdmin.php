<?php

namespace Perform\MailingListBundle\Admin;

use Perform\BaseBundle\Admin\AbstractAdmin;
use Perform\BaseBundle\Type\TypeConfig;

/**
 * SubscriberAdmin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SubscriberAdmin extends AbstractAdmin
{
    protected $routePrefix = 'perform_mailing_list_subscriber_';

    public function configureTypes(TypeConfig $config)
    {
        $config
            ->add('forename', [
                'type' => 'string',
                'contexts' => [
                    TypeConfig::CONTEXT_VIEW,
                    TypeConfig::CONTEXT_CREATE,
                    TypeConfig::CONTEXT_EDIT,
                ]
            ])
            ->add('surname', [
                'type' => 'string',
                'contexts' => [
                    TypeConfig::CONTEXT_VIEW,
                    TypeConfig::CONTEXT_CREATE,
                    TypeConfig::CONTEXT_EDIT,
                ]
            ])
            ->add('fullname', [
                'type' => 'string',
                'contexts' => [
                    TypeConfig::CONTEXT_LIST,
                ],
                'sort' => function($qb, $direction) {
                    return $qb->orderBy('e.forename', $direction)
                        ->addOrderBy('e.surname', $direction);
                },
            ])
            ->add('email', [
                'type' => 'email',
            ])
            ->add('createdAt', [
                'type' => 'datetime',
                'contexts' => [
                    TypeConfig::CONTEXT_LIST,
                    TypeConfig::CONTEXT_VIEW,
                ],
                'options' => [
                    'label' => 'Sign-up date',
                ],
            ])
            ;
    }
}
