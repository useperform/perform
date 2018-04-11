<?php

namespace Perform\RichContentBundle\Serializer;

use Perform\RichContentBundle\Entity\Block;
use Perform\RichContentBundle\BlockType\BlockTypeRegistry;
use Perform\RichContentBundle\Persister\OperationInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Perform\RichContentBundle\Persister\CreateOperation;
use Perform\RichContentBundle\Persister\UpdateOperation;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Perform\RichContentBundle\Repository\ContentRepository;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class OperationDenormalizer implements DenormalizerInterface
{
    protected $repo;

    public function __construct(ContentRepository $repo)
    {
        $this->repo = $repo;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        foreach (['blocks', 'newBlocks', 'order'] as $key) {
            if (!isset($data[$key])) {
                throw new InvalidArgumentException(sprintf('Missing required key "%s" to denormalize to a content update operation.', $key));
            }
        }

        $blockDefinitions = $data['blocks'];
        $newBlockDefinitions = $data['newBlocks'];
        $blockOrder = $data['order'];

        if (isset($data['contentId']) && $data['contentId']) {
            $content = $this->repo->find($data['contentId']);
            if (!$content) {
                throw new NotNormalizableValueException(sprintf('Unable to denormalize to a content update operation, content with id of "%s" was not found.', $data['contentId']));
            }
            return new UpdateOperation($content, $blockDefinitions, $newBlockDefinitions, $blockOrder);
        }

        if (!empty($blockDefinitions)) {
            throw new NotNormalizableValueException('The supplied data contains existing block data, implying an update operation, but no content id was given.');
        }

        return new CreateOperation($newBlockDefinitions, $blockOrder);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === OperationInterface::class;
    }
}
