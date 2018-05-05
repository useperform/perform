<?php

namespace Perform\MediaBundle\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Perform\MediaBundle\Importer\FileImporter;
use Perform\MediaBundle\Entity\File;

/**
 * Use block types to compute component_info for editor components to
 * use.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FileNormalizer implements NormalizerInterface
{
    protected $mediaManager;

    public function __construct(FileImporter $mediaManager)
    {
        $this->mediaManager = $mediaManager;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        // each type may want to define how it serializes the file
        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'url' => $this->mediaManager->getUrl($object),
            'status' => $object->getStatus(),
            'thumbnail' => $this->mediaManager->getSuitableUrl($object, ['width' => 100]),
            'type' => $object->getType(),
            'humanType' => ucfirst($object->getType()),
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof File;
    }
}
