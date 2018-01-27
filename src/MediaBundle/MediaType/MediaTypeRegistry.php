<?php

namespace Perform\MediaBundle\MediaType;

use Symfony\Component\DependencyInjection\ServiceLocator;
use Perform\MediaBundle\Exception\MediaTypeException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MediaTypeRegistry
{
    protected $locator;

    public function __construct(ServiceLocator $locator)
    {
        $this->locator = $locator;
    }

    public function get($typeName)
    {
        if (!$this->locator->has($typeName)) {
            throw new MediaTypeException(sprintf('Media type "%s" is not available.', $typeName));
        }

        return $this->locator->get($typeName);
    }
}
