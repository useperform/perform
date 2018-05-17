<?php

namespace Perform\BlogBundle\Crud;

use Perform\BaseBundle\Config\TypeConfig;

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
                    TypeConfig::CONTEXT_VIEW,
                    TypeConfig::CONTEXT_CREATE,
                    TypeConfig::CONTEXT_EDIT,
                ],
                'options' => [
                    'label' => 'Content',
                ]
            ])
            ;
    }
}
