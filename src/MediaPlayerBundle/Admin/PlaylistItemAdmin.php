<?php

namespace Perform\MediaPlayerBundle\Admin;

use Perform\BaseBundle\Admin\AbstractAdmin;
use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Config\LabelConfig;

/**
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

    public function configureLabels(LabelConfig $config)
    {
        $config->setEntityName('Playlist Track')
            ->setEntityLabel(function ($entity) {
                return $entity->getTitle();
            });
    }
}
