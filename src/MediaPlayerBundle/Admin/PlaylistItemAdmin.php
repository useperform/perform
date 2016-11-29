<?php

namespace Perform\MediaPlayerBundle\Admin;

use Perform\BaseBundle\Admin\AbstractAdmin;
use Perform\BaseBundle\Type\TypeConfig;

/**
 * PlaylistItemAdmin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PlaylistItemAdmin extends AbstractAdmin
{
    public function configureTypes(TypeConfig $config)
    {
        $config
            ->add('file', [
                'type' => 'image',
            ])
            ->add('title', [
                'type' => 'string',
            ])
            ;
    }

    public function getNameForEntity($entity)
    {
        return $entity->getFile()->getName();
    }
}
