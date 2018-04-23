<?php

namespace Perform\BaseBundle\Tests\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\BaseBundle\DependencyInjection\Compiler\DoctrinePass;
use Perform\BaseBundle\DependencyInjection\Doctrine;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DoctrinePassTest extends \PHPUnit_Framework_TestCase
{
    protected $pass;
    protected $container;

    public function setUp()
    {
        $this->pass = new DoctrinePass();
        $this->container = new ContainerBuilder();
    }

    public function testWithNoDefaults()
    {
        $config = [
            'SomeEntityInterface' => 'SomeEntity',
        ];
        $this->container->setParameter(Doctrine::PARAM_RESOLVED_CONFIG, $config);

        $this->pass->process($this->container);
        $this->assertSame($config, $this->container->getParameter(Doctrine::PARAM_RESOLVED));
        $this->assertFalse($this->container->hasParameter(Doctrine::PARAM_RESOLVED_DEFAULTS));
        $this->assertFalse($this->container->hasParameter(Doctrine::PARAM_RESOLVED_CONFIG));
    }

    public function testWithDefaults()
    {
        $defaults = [
            'SomeEntityInterface' => 'SomeBundleEntity',
            'AnimalInterface' => 'Dog',
        ];
        $this->container->setParameter(Doctrine::PARAM_RESOLVED_DEFAULTS, $defaults);
        $config = [
            'SomeEntityInterface' => 'SomeEntity',
            'AuthorInterface' => [
                'Post' => 'User',
                'Comment' => 'Visitor',
            ],
        ];
        $this->container->setParameter(Doctrine::PARAM_RESOLVED_CONFIG, $config);

        $this->pass->process($this->container);
        $expected = [
            'SomeEntityInterface' => 'SomeEntity',
            'AuthorInterface' => [
                'Post' => 'User',
                'Comment' => 'Visitor',
            ],
            'AnimalInterface' => 'Dog',
        ];
        $this->assertEquals($expected, $this->container->getParameter(Doctrine::PARAM_RESOLVED));
        $this->assertFalse($this->container->hasParameter(Doctrine::PARAM_RESOLVED_DEFAULTS));
        $this->assertFalse($this->container->hasParameter(Doctrine::PARAM_RESOLVED_CONFIG));
    }
}
