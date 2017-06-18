<?php

namespace Perform\BlogBundle\Admin;

use Perform\BaseBundle\Admin\AbstractAdmin;
use Perform\BaseBundle\Config\TypeConfig;

/**
 * PostAdmin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PostAdmin extends AbstractAdmin
{
    protected $routePrefix = 'perform_blog_post_';

    public function configureTypes(TypeConfig $config)
    {
        $config
            ->add('title', [
                'type' => 'string',
            ])
            ->add('publishDate', [
                'type' => 'date',
                'contexts' => [
                    TypeConfig::CONTEXT_VIEW,
                    TypeConfig::CONTEXT_CREATE,
                    TypeConfig::CONTEXT_EDIT,
                ],
            ])
            ->add('enabled', [
                'type' => 'boolean',
                'options' => [
                    'label' => 'Published',
                ],
            ])
            ->add('content', [
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
