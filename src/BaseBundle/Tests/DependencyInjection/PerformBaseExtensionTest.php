<?php

namespace Perform\BaseBundle\Tests\DependencyInjection;

use Perform\BaseBundle\DependencyInjection\PerformBaseExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\BaseBundle\Menu\SimpleLinkProvider;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformBaseExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->ext = new PerformBaseExtension();
    }

    public function testSimpleMenus()
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', true);
        $container->setParameter('kernel.bundles', []);
        $config = [
            'menu' => [
                'simple' => [
                    'test' => [
                        'route' => 'some_route',
                    ]
                ]
            ],
        ];
        $this->ext->load([$config], $container);

        $menuService = $container->getDefinition('perform_base.menu.simple.test');
        $this->assertSame(SimpleLinkProvider::class, $menuService->getClass());
        $this->assertSame('some_route', $menuService->getArgument(2));
    }
}
