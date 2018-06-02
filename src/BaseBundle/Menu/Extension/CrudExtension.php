<?php

namespace Perform\BaseBundle\Menu\Extension;

use Knp\Menu\Factory\ExtensionInterface;
use Knp\Menu\ItemInterface;
use Perform\BaseBundle\Routing\CrudUrlGenerator;
use Perform\BaseBundle\Crud\CrudRequest;

/**
 * Adds a 'crud' menu option to automatically route to a crud service.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudExtension implements ExtensionInterface
{
    public function __construct(CrudUrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function buildOptions(array $options = [])
    {
        if (isset($options['crud'])) {
            $options['route'] = $this->urlGenerator->getRouteName($options['crud'], CrudRequest::CONTEXT_LIST);
        }

        return $options;
    }

    public function buildItem(ItemInterface $item, array $options)
    {
    }
}
