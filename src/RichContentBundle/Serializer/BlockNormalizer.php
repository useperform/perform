<?php

namespace Perform\RichContentBundle\Serializer;

use Perform\RichContentBundle\Entity\Block;
use Perform\RichContentBundle\BlockType\BlockTypeRegistry;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Use block types to compute component_info for editor components to
 * use.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BlockNormalizer implements NormalizerInterface
{
    protected $registry;

    public function __construct(BlockTypeRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        return [
            'id' => $object->getId(),
            'type' => $type = $object->getType(),
            'value' => $object->getValue(),
            'component_info' => (object) $this->registry->get($type)->getComponentInfo($object),
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Block;
    }
}
