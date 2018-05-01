<?php

namespace Perform\BaseBundle\Tests\DependencyInjection;

use Perform\BaseBundle\DependencyInjection\PerformBaseExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * PerformBaseExtensionTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformBaseExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->ext = new PerformBaseExtension();
    }

    public function testAdminAliasesAreResolved()
    {
        $container = new ContainerBuilder();
        $config = [
            'admins' => [
                'SomeBundle:Item' => [
                    'types' => [
                        'slug' => ['type' => 'string']
                    ]
                ]
            ],
        ];
        $container->setParameter('perform_base.entity_aliases', [
            'SomeBundle:Item' => 'SomeBundle\Entity\Item',
        ]);
        $this->ext->processAdminConfig($container, $config);
        $expected = [
            'SomeBundle\Entity\Item' => [
                'types' => [
                    'slug' => ['type' => 'string']
                ]
            ]
        ];

        $this->assertSame($expected, $container->getParameter('perform_base.admins'));
    }
}
