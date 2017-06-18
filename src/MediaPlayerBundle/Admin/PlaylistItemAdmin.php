<?php

namespace Perform\MediaPlayerBundle\Admin;

use Perform\BaseBundle\Admin\AbstractAdmin;
use Perform\BaseBundle\Config\TypeConfig;

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
                'type' => 'media',
                'options' => [
                    'types' => 'audio',
                ],
            ])
            ->add('title', [
                'type' => 'string',
            ])
            ->add('sortOrder', [
                'type' => 'hidden',
            ])
            ;
    }

    public function getNameForEntity($entity)
    {
        return $entity->getFile()->getName();
    }
}
