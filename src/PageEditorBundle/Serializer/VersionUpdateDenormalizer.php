<?php

namespace Perform\PageEditorBundle\Serializer;

use Perform\RichContentBundle\Persister\OperationInterface;
use Perform\PageEditorBundle\Repository\VersionRepository;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Perform\PageEditorBundle\Persister\VersionUpdate;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class VersionUpdateDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    protected $repo;

    public function __construct(VersionRepository $repo)
    {
        $this->repo = $repo;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        foreach (['versionId', 'sections'] as $key) {
            if (!isset($data[$key])) {
                throw new InvalidArgumentException(sprintf('Missing required key "%s" to denormalize to a version update.', $key));
            }
        }
        if (!is_array($data['sections'])) {
            throw new InvalidArgumentException('The sections key must be an array.');
        }

        $version = $this->repo->find($data['versionId']);
        if (!$version) {
            throw new NotNormalizableValueException(sprintf('Unable to denormalize to a version update, version with id of "%s" was not found.', $data['versionId']));
        }

        $sectionNames = [];
        $operations = [];
        foreach ($data['sections'] as $sectionName => $sectionData) {
            $sectionNames[] = $sectionName;
            $operations[] = $this->denormalizer->denormalize($sectionData, OperationInterface::class);
        }

        return new VersionUpdate($version, $sectionNames, $operations);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === VersionUpdate::class;
    }
}
