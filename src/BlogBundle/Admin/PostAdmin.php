<?php

namespace Perform\BlogBundle\Admin;

use Perform\Base\Admin\AbstractAdmin;

/**
 * PostAdmin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PostAdmin extends AbstractAdmin
{
    protected $listFields = [
        'title',
        'enabled',
    ];
    protected $viewFields = [
        'title',
        'publishDate',
        'enabled',
        'content',
    ];
    protected $createFields = [
        'title',
        'publishDate',
        'enabled',
        'content',
    ];
    protected $editFields = [
        'title',
        'publishDate',
        'enabled',
        'content',
    ];
    protected $fieldOptions = [
        'enabled' => [
            'type' => 'boolean',
        ],
        'publishDate' => [
            'type' => 'date',
        ],
        'content' => [
            'type' => 'text',
        ],
    ];
    protected $routePrefix = 'admin_blog_post_';
}
