<?php

namespace Perform\BaseBundle\Tests\DependencyInjection;

use Perform\BaseBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->config = new Configuration();
    }

    protected function process(array $configs)
    {
        $p = new Processor();

        return $p->processConfiguration($this->config, $configs);
    }

    public function testMenuDefaults()
    {
        $config = $this->process([]);
        $expected = [
            'simple' => [],
            'order' => [],
        ];

        $this->assertSame($expected, $config['menu']);
    }

    public function testExtendEntities()
    {
        $config = $this->process([]);
        $this->assertSame([], $config['extended_entities']);

        $entities = [
            'ParentBundle\Entity\Bar' => 'ChildBundle\Entity\Bar',
            'ParentBundle\\Entity\\Foo' => 'ChildBundle\\Entity\\Foo',
        ];
        $config = $this->process([
            'perform_base' => [
                'extended_entities' => $entities,
            ],
        ]);

        $this->assertSame($entities, $config['extended_entities']);
    }

    public function testResolveEntities()
    {
        $config = $this->process([]);
        $this->assertSame([], $config['doctrine']['resolve']);

        $entities = [
            'AppBundle\Entity\UserInterface' => 'AppBundle\Entity\User',
            'AppBundle\Entity\AuthorInterface' => [
                'AppBundle\Entity\BlogPost' => 'AppBundle\Entity\User',
                'AppBundle\Entity\Comment' => 'AppBundle\Entity\Visitor',
            ],
        ];
        $config = $this->process([
            'perform_base' => [
                'doctrine' => [
                    'resolve' => $entities,
                ],
            ],
        ]);

        $this->assertSame($entities, $config['doctrine']['resolve']);
    }

    public function invalidResolveProvider()
    {
        return [
            [[
                'AppBundle\Entity\UserInterface' => true,
            ]],
            [[
                'AppBundle\Entity\UserInterface' => [],
            ]],
            [[
                'AppBundle\Entity\UserInterface' => ['SomeClass', 'OtherClass'],
            ]],
            [[
                'AppBundle\Entity\UserInterface' => [
                    'AppBundle\Entity\Group' => 'AppBundle\Entity\User',
                    'AppBundle\Entity\AdminUser',
                ],
            ]],
        ];
    }

    /**
     * @dataProvider invalidResolveProvider
     */
    public function testInvalidResolveEntities($invalid)
    {
        $this->setExpectedException(InvalidConfigurationException::class);
        $this->process([
            'perform_base' => [
                'doctrine' => [
                    'resolve' => $invalid,
                ],
            ],
        ]);
    }

    public function testAssetDefaults()
    {
        $config = $this->process([]);
        $expected = [
            'entrypoints' => [],
            'namespaces' => [],
            'extra_js' => [],
            'extra_sass' => [],
            'theme' => '~perform-base/scss/themes/default',
        ];

        $this->assertSame($expected, $config['assets']);
    }

    public function testValidAssetEntrypoints()
    {
        $config = $this->process([
            'perform_base' => [
                'assets' => [
                    'entrypoints' => [
                        'site' => 'site.js',
                    ],
                ],
            ],
        ]);
        $this->assertSame('site.js', $config['assets']['entrypoints']['site']);

        $config = $this->process([
            'perform_base' => [
                'assets' => [
                    'entrypoints' => [
                        'site' => ['site.js', 'site.scss'],
                    ],
                ],
            ],
        ]);
        $this->assertSame(['site.js', 'site.scss'], $config['assets']['entrypoints']['site']);
    }

    public function testInvalidAssetEntrypoints()
    {
        $this->setExpectedException(InvalidConfigurationException::class);
        $this->process([
            'perform_base' => [
                'assets' => [
                    'entrypoints' => [
                        'site' => ['site.js', []],
                    ],
                ],
            ],
        ]);
    }
}
