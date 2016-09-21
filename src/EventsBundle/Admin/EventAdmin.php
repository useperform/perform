<?php

namespace Perform\EventsBundle\Admin;

use Perform\Base\Admin\AbstractAdmin;

/**
 * EventAdmin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EventAdmin extends AbstractAdmin
{
    protected $listFields = [
        'title',
        'startTime',
    ];
    protected $viewFields = [
        'title',
        'slug',
        'startTime',
        'location',
        'image',
        'description',
    ];
    protected $createFields = [
        'title',
        'slug',
        'startTime',
        'location',
        'image',
        'description',
    ];
    protected $editFields = [
        'title',
        'slug',
        'startTime',
        'location',
        'image',
        'description',
    ];
    protected $fieldOptions = [
        'startTime' => [
            'type' => 'datetime',
        ],
        'description' => [
            'type' => 'text',
        ],
        'image' => [
            'type' => 'image',
        ],
    ];
    protected $listFieldOptions = [
        'startTime' => [
            'human' => false
        ],
    ];
    protected $routePrefix = 'perform_events_events_';
}
