<?php

namespace Perform\EventsBundle\Admin;

use Perform\BaseBundle\Admin\AbstractAdmin;
use Perform\BaseBundle\Config\TypeConfig;

/**
 * EventAdmin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EventAdmin extends AbstractAdmin
{
    protected $routePrefix = 'perform_events_events_';

    public function configureTypes(TypeConfig $config)
    {
        $config
            ->add('title', [
                'type' => 'string',
            ])
            ->add('slug', [
                'type' => 'string',
                'contexts' => [
                    TypeConfig::CONTEXT_VIEW,
                    TypeConfig::CONTEXT_CREATE,
                    TypeConfig::CONTEXT_EDIT,
                ],
            ])
            ->add('startTime', [
                'type' => 'datetime',
                'options' => [
                    'human' => false,
                ],
            ])
            ->add('location', [
                'type' => 'string',
                'contexts' => [
                    TypeConfig::CONTEXT_VIEW,
                    TypeConfig::CONTEXT_CREATE,
                    TypeConfig::CONTEXT_EDIT,
                ],
            ])
            ->add('image', [
                'type' => 'media',
                'contexts' => [
                    TypeConfig::CONTEXT_VIEW,
                    TypeConfig::CONTEXT_CREATE,
                    TypeConfig::CONTEXT_EDIT,
                ],
                'options' => [
                    'types' => 'image',
                ]
            ])
            ->add('description', [
                'type' => 'text',
                'contexts' => [
                    TypeConfig::CONTEXT_VIEW,
                    TypeConfig::CONTEXT_CREATE,
                    TypeConfig::CONTEXT_EDIT,
                ],
            ])
            ;
    }
}
