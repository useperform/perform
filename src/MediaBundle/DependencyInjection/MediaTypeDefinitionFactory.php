<?php

namespace Perform\MediaBundle\DependencyInjection;

use Imagine\Gd\Imagine;
use Perform\MediaBundle\Exception\MediaTypeException;
use Perform\MediaBundle\MediaType\AudioType;
use Perform\MediaBundle\MediaType\ImageType;
use Perform\MediaBundle\MediaType\OtherType;
use Perform\MediaBundle\MediaType\PdfType;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MediaTypeDefinitionFactory
{
    public function create(array $config)
    {
        switch ($config['type']) {
        case 'image':
            $definition = new Definition(ImageType::class);
            $definition->setArguments([
                new Definition(Imagine::class),
                $config['widths'],
            ]);

            return $definition;
        case 'pdf':
            return new Definition(PdfType::class);
        case 'audio':
            return new Definition(AudioType::class);
        case 'other':
            return new Definition(OtherType::class);
        default:
            throw new MediaTypeException(sprintf('Unknown media type "%s" requested.', $config['type']));
        }
    }
}
