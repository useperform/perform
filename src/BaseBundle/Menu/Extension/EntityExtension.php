<?php

namespace Perform\BaseBundle\Menu\Extension;

use Knp\Menu\Factory\ExtensionInterface;
use Knp\Menu\ItemInterface;
use Perform\BaseBundle\Routing\CrudUrlGenerator;

/**
 * EntityExtension
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntityExtension implements ExtensionInterface
{
    public function __construct(CrudUrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function buildOptions(array $options = array())
    {
        if (isset($options['entity'])) {
            $options['route'] = $this->urlGenerator->getDefaultEntityRoute($options['entity']);
        }

        return $options;
    }

    public function buildItem(ItemInterface $item, array $options)
    {
    }
}
