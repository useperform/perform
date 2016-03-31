<?php

namespace Admin\MediaBundle\Plugin;

use Admin\MediaBundle\Entity\File;
use Admin\MediaBundle\Exception\PluginNotFoundException;

/**
 * PluginRegistry
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PluginRegistry
{
    protected $plugins = [];

    public function addPlugin(FilePluginInterface $plugin)
    {
        $this->plugins[$plugin->getName()] = $plugin;
    }

    public function getPlugin($name)
    {
        if (!isset($this->plugins[$name])) {
            throw new PluginNotFoundException(sprintf('File plugin not found: "%s"', $name));
        }

        return $this->plugins[$name];
    }

    public function hasPlugin($name)
    {
        return isset($this->plugins[$name]);
    }

    public function getPlugins()
    {
        return $this->plugins;
    }

    public function onFileCreate(File $file)
    {
        foreach ($this->plugins as $plugin) {
            $plugin->onCreate($file);
            if ($file->hasType()) {
                return;
            }
        }
    }

    public function onFileProcess(File $file)
    {
        foreach ($this->plugins as $plugin) {
            $plugin->onProcess($file);
        }
    }

    public function onFileDelete(File $file)
    {
        foreach ($this->plugins as $plugin) {
            $plugin->onDelete($file);
        }
    }

    /**
     * Get the absolute url to a stored file entity.
     *
     * @param File
     */
    public function getUrl(File $file = null)
    {
        if (!$file) {
            return '';
        }

        if (isset($this->plugins[$file->getType()])) {
            return $this->plugins[$file->getType()]->getUrl($file);
        }
        return $file->filename;
    }

    /**
     * Get an HTML preview of a file entity.
     *
     * @param File
     */
    public function getPreview(File $file = null)
    {
        $type = $file->getType();
        if (!$file || !isset($this->plugins[$type])) {
            return '<i class="fa fa-file-o"></i>';
        }

        return $this->plugins[$type]->getPreview($file);
    }
}
