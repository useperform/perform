<?php

namespace Admin\MediaBundle\Twig\Extension;

use Admin\MediaBundle\Entity\File;
use Admin\MediaBundle\Plugin\PluginRegistry;

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
