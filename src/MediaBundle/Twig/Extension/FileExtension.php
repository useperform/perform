<?php

namespace Perform\MediaBundle\Twig\Extension;

use Perform\MediaBundle\Entity\File;
use Perform\MediaBundle\Plugin\PluginRegistry;

/**
 * FileExtension
 *
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
            new \Twig_SimpleFunction('fileListingType', [$this->registry, 'getListingType']),
            new \Twig_SimpleFunction('fileUrl', [$this->registry, 'getUrl']),
            new \Twig_SimpleFunction('filePreview', [$this->registry, 'getPreview'], ['is_safe' => ['html']]),
        ];
    }

    public function getName()
    {
        return 'file';
    }
}
