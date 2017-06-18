<?php

namespace Perform\MediaPlayerBundle\Admin;

use Perform\BaseBundle\Admin\AbstractAdmin;
use Perform\BaseBundle\Config\TypeConfig;

/**
 * PlaylistAdmin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PlaylistAdmin extends AbstractAdmin
{
    protected $routePrefix = 'perform_mediaplayer_playlist_';

    public function configureTypes(TypeConfig $config)
    {
        $config
            ->add('title', [
                'type' => 'string',
            ])
            ->add('items', [
                'type' => 'collection',
                'options' => [
                    'label' => 'Tracks',
                    'itemLabel' => 'track',
                    'entity' => 'PerformMediaPlayerBundle:PlaylistItem',
                    'sortField' => 'sortOrder',
                ]
            ])
            ;
    }
}
