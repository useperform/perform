<?php

namespace Perform\MediaBundle\Twig\Extension;

use Perform\MediaBundle\Entity\File;
use Perform\MediaBundle\Importer\FileImporter;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Perform\BaseBundle\Asset\AssetContainer;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FileExtension extends \Twig_Extension
{
    protected $mediaManager;
    protected $normalizer;
    protected $assets;

    public function __construct(FileImporter $mediaManager, NormalizerInterface $normalizer, AssetContainer $assets)
    {
        $this->mediaManager = $mediaManager;
        $this->normalizer = $normalizer;
        $this->assets = $assets;
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
        if (!$file) {
            return '';
        }

        $data = json_encode($this->normalizer->normalize($file));
        $id = sprintf('media-preview-%s-%s', $file->getId(), uniqid());
        $js = <<<EOF
Perform.media.preview("#{$id}", {$data});
EOF;
        $this->assets->addInlineJs($js);

        return $twig->render('@PerformMedia/file/_preview.html.twig', [
            'id' => $id,
        ]);
    }
}
