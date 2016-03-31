<?php

namespace Admin\MediaBundle\Plugin;

use Admin\MediaBundle\Entity\File;

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
     * Get the absolute url to a stored file entity.
     *
     * @param File
     */
    public function getUrl(File $file);

    /**
     * Get an HTML preview of a file entity.
     *
     * @param File
     */
    public function getPreview(File $file);

    public function onCreate(File $file);

    public function onProcess(File $file);

    public function onDelete(File $file);
}
