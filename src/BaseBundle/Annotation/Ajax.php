<?php

namespace Perform\BaseBundle\Annotation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;

/**
 * Ajax
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 * @Annotation
 **/
class Ajax implements ConfigurationInterface
{
    public function __construct(array $configuration)
    {
    }

    public function getAliasName()
    {
        return 'ajax';
    }

    public function allowArray()
    {
        return false;
    }
}
