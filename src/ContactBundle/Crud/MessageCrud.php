<?php

namespace Perform\ContactBundle\Crud;

use Perform\BaseBundle\Crud\AbstractCrud;
use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Config\FilterConfig;
use Perform\BaseBundle\Config\ActionConfig;
use Perform\ContactBundle\Entity\Message;
use Perform\BaseBundle\Crud\CrudRequest;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MessageCrud extends AbstractCrud
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
                    CrudRequest::CONTEXT_LIST,
                    CrudRequest::CONTEXT_VIEW,
                    CrudRequest::CONTEXT_EXPORT,
                ],
            ])
            ->add('email', [
                'type' => 'string',
                'contexts' => [
                    CrudRequest::CONTEXT_LIST,
                    CrudRequest::CONTEXT_VIEW,
                    CrudRequest::CONTEXT_EXPORT,
                ],
            ])
            ->add('createdAt', [
                'type' => 'datetime',
                'contexts' => [
                    CrudRequest::CONTEXT_LIST,
                    CrudRequest::CONTEXT_VIEW,
                    CrudRequest::CONTEXT_EXPORT,
                ],
                'options' => [
                    'label' => 'Sent at',
                ],
            ])
            ->add('message', [
                'type' => 'text',
                'contexts' => [
                    CrudRequest::CONTEXT_VIEW,
                    CrudRequest::CONTEXT_EXPORT,
                ],
            ])
            ->setDefaultSort('createdAt', 'DESC')
            ;
    }

    public function configureFilters(FilterConfig $config)
    {
        $config->add('new', [
            'query' => function($qb) {
                return $qb->andWhere('e.status = :status')
                    ->setParameter('status', Message::STATUS_NEW);
            },
            'count' => true,
        ]);
        $config->add('archive', [
            'query' => function($qb) {
                return $qb->andWhere('e.status = :status')
                    ->setParameter('status', Message::STATUS_ARCHIVE);
            },
        ]);
        $config->add('spam', [
            'query' => function($qb) {
                return $qb->andWhere('e.status = :status')
                    ->setParameter('status', Message::STATUS_SPAM);
            },
        ]);
        $config->setDefault('new');
    }

    public function configureActions(ActionConfig $config)
    {
        $this->addViewAction($config);
        $config->add('perform_contact_archive');
        $config->add('perform_contact_new');
        $config->add('perform_contact_spam');
    }
}
