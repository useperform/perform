<?php

namespace Perform\MediaBundle\Plugin;

use Symfony\Component\DependencyInjection\ServiceLocator;
use Perform\MediaBundle\Exception\MediaTypeException;

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
            throw new MediaTypeException(sprintf('Media type "%s" is not available.', $pluginName));
        }

        return $this->locator->get($pluginName);
    }
}
