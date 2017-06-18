<?php

namespace Perform\MusicBundle\Admin;

use Perform\BaseBundle\Admin\AbstractAdmin;
use Perform\BaseBundle\Config\TypeConfig;

/**
 * CompositionAdmin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CompositionAdmin extends AbstractAdmin
{
    protected $routePrefix = 'perform_music_composition_';

    public function configureTypes(TypeConfig $config)
    {
        $config
            ->add('title', [
                'type' => 'string',
            ])
            ->add('publishDate', [
                'type' => 'date',
                'options' => [
                    'label' => 'Date',
                ]
            ])
            ->add('description', [
                'type' => 'text',
                'contexts' => [
                    TypeConfig::CONTEXT_VIEW,
                    TypeConfig::CONTEXT_CREATE,
                    TypeConfig::CONTEXT_EDIT,
                ]
            ])

            //optional fields, contexts are disabled by default
            ->add('category', [
                'type' => 'string',
                'contexts' => []
            ])
            ->add('duration', [
                'type' => 'duration',
                'contexts' => []
            ])
            ->add('instruments', [
                'type' => 'string',
                'contexts' => []
            ])
            ;
    }
}
