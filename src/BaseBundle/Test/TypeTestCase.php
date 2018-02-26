<?php

namespace Perform\BaseBundle\Test;

use Perform\BaseBundle\Type\TypeRegistry;
use Perform\BaseBundle\Admin\ContextRenderer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Environment;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
abstract class TypeTestCase extends \PHPUnit_Framework_TestCase
{
    protected $kernel;
    protected $typeRegistry;
    protected $renderer;
    protected $container;

    public function setUp()
    {
        // create a kernel to get full access to the twig environment
        $this->kernel = new TestKernel();
        $this->kernel->boot();
        $twig = $this->kernel->getContainer()->get('twig');

        // but create a fresh type registry and container for testing
        $this->container = $this->getMock(ContainerInterface::class);
        $this->typeRegistry = new TypeRegistry($this->container);
        $this->renderer = new ContextRenderer($this->typeRegistry, $twig);
        $this->configure();
    }

    protected function mockService($name, $object)
    {
        $this->container->expects($this->any())
            ->method('get')
            ->with($name)
            ->will($this->returnValue($object));
    }

    public function tearDown()
    {
        $this->kernel->shutdown();
    }

    abstract protected function configure();
}
