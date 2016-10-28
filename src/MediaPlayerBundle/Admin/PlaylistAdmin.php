<?php

namespace Perform\MediaPlayerBundle\Admin;

use Perform\BaseBundle\Admin\AbstractAdmin;
use Perform\BaseBundle\Type\TypeConfig;

/**
 * PlaylistAdmin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PlaylistAdmin extends AbstractAdmin
{
    protected $routePrefix = 'perform_media_player_playlist_';

    public function configureTypes(TypeConfig $config)
    {
        $config
            ->add('title', [
                'type' => 'string',
            ])
            ;
    }
}
