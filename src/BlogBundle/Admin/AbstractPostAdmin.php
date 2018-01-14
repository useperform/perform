<?php

namespace Perform\BlogBundle\Admin;

use Perform\BaseBundle\Admin\AbstractAdmin;
use Perform\BaseBundle\Config\TypeConfig;
use Perform\BlogBundle\Entity\AbstractPost;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
abstract class AbstractPostAdmin extends AbstractAdmin
{
    public function configureTypes(TypeConfig $config)
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
