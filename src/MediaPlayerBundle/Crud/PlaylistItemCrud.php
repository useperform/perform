<?php

namespace Perform\MediaPlayerBundle\Crud;

use Perform\BaseBundle\Crud\AbstractCrud;
use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Config\LabelConfig;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PlaylistItemCrud extends AbstractCrud
{
    public function configureTypes(TypeConfig $config)
    {
        $config
            ->add('file', [
                'type' => 'media',
                'options' => [
                    'types' => 'audio',
                    'use_selector' => false,
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
