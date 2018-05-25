<?php

namespace Perform\MediaPlayerBundle\Crud;

use Perform\BaseBundle\Crud\AbstractCrud;
use Perform\BaseBundle\Config\TypeConfig;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PlaylistCrud extends AbstractCrud
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
                    'crud_name' => 'perform_media_player.playlist_item',
                    'sortField' => 'sortOrder',
                ]
            ])
            ;
    }
}
