<?php

namespace Perform\BlogBundle\Crud;

use Perform\BaseBundle\Config\FieldConfig;
use Perform\BaseBundle\Crud\CrudRequest;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MarkdownPostCrud extends AbstractPostCrud
{
    public function configureFields(FieldConfig $config)
    {
        parent::configureFields($config);
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
