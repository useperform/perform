<?php

namespace Perform\MediaBundle\Twig\Extension;

use Perform\MediaBundle\Entity\File;
use Perform\MediaBundle\Importer\FileImporter;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FileExtension extends \Twig_Extension
{
    protected $mediaManager;

    public function __construct(FileImporter $mediaManager)
    {
        $this->mediaManager = $mediaManager;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_media_url', [$this->mediaManager, 'getSuitableUrl']),
            new \Twig_SimpleFunction('perform_media_preview', [$this, 'getPreview'], ['is_safe' => ['html'], 'needs_environment' => true]),
        ];
    }

    public function getPreview(\Twig_Environment $twig, File $file = null)
    {
        $data = $file ? [
            'id' => $file->getId(),
            'name' => $file->getName(),
            'url' => $this->mediaManager->getUrl($file),
            'thumbnail' => $this->mediaManager->getSuitableUrl($file, ['width' => 400]),
            'type' => $file->getType(),
        ] : [];

        return $twig->render('@PerformMedia/file/_preview.html.twig', [
            'file' => $file,
            'data' => $data,
        ]);
    }
}
