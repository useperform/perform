<?php

namespace Perform\MediaBundle\Plugin;

use Symfony\Component\DependencyInjection\ServiceLocator;
use Perform\MediaBundle\Exception\PluginNotFoundException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PluginRegistry
{
    protected $locator;

    public function __construct(ServiceLocator $locator)
    {
        $this->locator = $locator;
    }

    public function get($pluginName)
    {
        if (!$this->locator->has($pluginName)) {
            throw new PluginNotFoundException(sprintf('Media plugin "%s" was not found.', $pluginName));
        }

        return $this->locator->get($pluginName);
    }
}
