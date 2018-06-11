<?php

namespace Perform\BlogBundle\Crud;

use Perform\BaseBundle\Crud\AbstractCrud;
use Perform\BaseBundle\Config\FieldConfig;
use Perform\BlogBundle\Entity\AbstractPost;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
abstract class AbstractPostCrud extends AbstractCrud
{
    public function configureFields(FieldConfig $config)
    {
        $config
            ->add('title', [
                'type' => 'string',
            ])
            ->add('slug', [
                'type' => 'slug',
                'options' => [
                    'target' => 'title',
                ]
            ])
            ->add('publishDate', [
                'type' => 'date',
            ])
            ->add('status', [
                'type' => 'choice',
                'options' => [
                    'choices' => [
                        'Draft' => AbstractPost::STATUS_DRAFT,
                        'Published' => AbstractPost::STATUS_PUBLISHED,
                    ]
                ],
            ])
            ;
    }
}
