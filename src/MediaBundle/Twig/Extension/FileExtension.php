<?php

namespace Perform\MediaBundle\Twig\Extension;

use Perform\MediaBundle\Entity\File;
use Perform\MediaBundle\Plugin\PluginRegistry;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FileExtension extends \Twig_Extension
{
    protected $registry;

    public function __construct(PluginRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_file_listing_type', [$this->registry, 'getListingType']),
            new \Twig_SimpleFunction('perform_file_url', [$this->registry, 'getUrl']),
            new \Twig_SimpleFunction('perform_media_preview', [$this, 'getPreview'], ['is_safe' => ['html'], 'needs_environment' => true]),
        ];
    }

    public function getPreview(\Twig_Environment $twig, File $file = null)
    {
        $data = $file ? [
            'id' => $file->getId(),
            'name' => $file->getName(),
            'filename' => $file->getFilename(),
            'type' => $file->getType(),
        ] : [];

        return $twig->render('@PerformMedia/file/_preview.html.twig', [
            'file' => $file,
            'data' => $data,
        ]);
    }
}
