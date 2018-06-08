<?php

namespace Perform\BlogBundle\EventListener;

use Knp\Menu\ItemInterface;
use Perform\BaseBundle\Menu\LinkProviderInterface;
use Perform\BaseBundle\Routing\CrudUrlGeneratorInterface;
use Perform\BaseBundle\Event\MenuEvent;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BlogMenuListener
{
    protected $urlGenerator;

    public function __construct(CrudUrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function onMenuBuild(MenuEvent $event)
    {
        if ($event->getMenuName() !== 'perform_sidebar') {
            return;
        }
        if (!$this->urlGenerator->routeExists('perform_blog.markdown_post', 'list')) {
            return;
        }

        $menu = $event->getMenu();
        $menu->addChild('blog', [
            'crud' => 'perform_blog.markdown_post',
        ])->setExtra('icon', 'newspaper-o');
    }
}
