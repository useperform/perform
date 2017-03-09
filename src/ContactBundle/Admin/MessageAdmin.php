<?php

namespace Perform\ContactBundle\Admin;

use Perform\BaseBundle\Admin\AbstractAdmin;
use Perform\BaseBundle\Type\TypeConfig;
use Perform\BaseBundle\Filter\FilterConfig;
use Perform\ContactBundle\Entity\Message;

/**
 * MessageAdmin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MessageAdmin extends AbstractAdmin
{
    protected $routePrefix = 'perform_contact_message_';

    public function getActions()
    {
        return [
            '/' => 'list',
            '/view/{id}' => 'view',
        ];
    }

    public function configureTypes(TypeConfig $config)
    {
        $config
            ->add('name', [
                'type' => 'string',
                'contexts' => [
                    TypeConfig::CONTEXT_LIST,
                    TypeConfig::CONTEXT_VIEW,
                ],
            ])
            ->add('email', [
                'type' => 'string',
                'contexts' => [
                    TypeConfig::CONTEXT_LIST,
                    TypeConfig::CONTEXT_VIEW,
                ],
            ])
            ->add('createdAt', [
                'type' => 'datetime',
                'contexts' => [
                    TypeConfig::CONTEXT_LIST,
                    TypeConfig::CONTEXT_VIEW,
                ],
                'options' => [
                    'label' => 'Sent at',
                ],
            ])
            ->add('message', [
                'type' => 'text',
                'contexts' => [
                    TypeConfig::CONTEXT_VIEW,
                ],
            ])
            ->setDefaultSort('createdAt', 'DESC')
            ;
    }

    public function configureFilters(FilterConfig $config)
    {
        $config->add('new', [
            'query' => function($qb) {
                return $qb->where('e.status = :status')
                    ->setParameter('status', Message::STATUS_NEW);
            },
            'count' => true,
        ]);
        $config->add('archive', [
            'query' => function($qb) {
                return $qb->where('e.status = :status')
                    ->setParameter('status', Message::STATUS_ARCHIVE);
            },
        ]);
        $config->add('spam', [
            'query' => function($qb) {
                return $qb->where('e.status = :status')
                    ->setParameter('status', Message::STATUS_SPAM);
            },
        ]);
        $config->setDefault('new');
    }
}
