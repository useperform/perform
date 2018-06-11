<?php

namespace Perform\BaseBundle\Test;

use Perform\BaseBundle\FieldType\FieldTypeRegistry;
use Perform\BaseBundle\Crud\ContextRenderer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Environment;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
abstract class FieldTypeTestCase extends \PHPUnit_Framework_TestCase
{
    protected $kernel;
    protected $typeRegistry;
    protected $renderer;

    public function setUp()
    {
        // create a kernel to get full access to the twig environment
        $this->kernel = new TestKernel();
        $this->kernel->boot();
        $twig = $this->kernel->getContainer()->get('twig');

        // but create a fresh type registry and container for testing
        $this->typeRegistry = Services::typeRegistry($this->registerTypes());
        $this->renderer = new ContextRenderer($this->typeRegistry, $twig);
    }

    public function tearDown()
    {
        $this->kernel->shutdown();
    }

    /**
     * @return TypeInterface[]
     */
    abstract protected function registerTypes();
}
