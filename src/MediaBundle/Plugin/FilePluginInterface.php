<?php

namespace Perform\MediaBundle\Plugin;

use Perform\MediaBundle\Entity\File;

/**
 * FilePluginInterface.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface FilePluginInterface
{
    /**
     * Get the name of this plugin. The name will be used in the
     * 'type' column of the file table.
     *
     * @return string
     */
    public function getName();

    public function getListingName();

    /**
     * Get an HTML preview of a file entity.
     *
     * @param File
     */
    public function getPreview(File $file, array $options = []);

    public function onCreate(File $file);

    public function onProcess(File $file);

    public function onDelete(File $file);
}
