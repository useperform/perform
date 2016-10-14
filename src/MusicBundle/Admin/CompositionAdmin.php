<?php

namespace Perform\MusicBundle\Admin;

use Perform\BaseBundle\Admin\AbstractAdmin;

/**
 * CompositionAdmin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CompositionAdmin extends AbstractAdmin
{
    protected $listFields = [
        'title',
        'publishDate',
    ];
    protected $viewFields = [
        'title',
        'publishDate',
        'description',
    ];
    protected $createFields = [
        'title',
        'publishDate',
        'description',
    ];
    protected $editFields = [
        'title',
        'publishDate',
        'description',
    ];
    protected $fieldOptions = [
        'publishDate' => [
            'type' => 'date',
            'label' => 'Date',
        ],
        'description' => [
            'type' => 'text',
        ],
    ];
    protected $routePrefix = 'perform_music_composition_';
}
