<?php

namespace Perform\BlogBundle\Crud;

use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Crud\CrudRequest;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MarkdownPostCrud extends AbstractPostCrud
{
    protected $routePrefix = 'perform_blog_markdown_post_';

    public function configureTypes(TypeConfig $config)
    {
        parent::configureTypes($config);
        $config
            ->add('markdown', [
                'type' => 'markdown',
                'contexts' => [
                    CrudRequest::CONTEXT_VIEW,
                    CrudRequest::CONTEXT_CREATE,
                    CrudRequest::CONTEXT_EDIT,
                ],
                'options' => [
                    'label' => 'Content',
                ]
            ])
            ;
    }
}
