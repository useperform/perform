<?php

namespace Perform\BaseBundle\Test;

use PHPUnit\Framework\TestCase;
use Perform\BaseBundle\FieldType\FieldTypeRegistry;
use Perform\BaseBundle\Crud\ContextRenderer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Environment;
use Perform\BaseBundle\Config\FieldConfig;
use Perform\BaseBundle\Crud\CrudRequest;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
abstract class FieldTypeTestCase extends TestCase
{
    protected $kernel;
    protected $typeRegistry;
    protected $config;
    protected $renderer;

    protected function createTestKernel()
    {
        return new TestKernel();
    }

    public function setUp()
    {
        // create a kernel to get full access to the twig environment
        $this->kernel = $this->createTestKernel();
        $this->kernel->boot();
        $twig = $this->kernel->getContainer()->get('twig');

        // but create a fresh type registry, config, and renderer for testing
        $this->typeRegistry = Services::fieldTypeRegistry($this->registerTypes());
        $this->config = new FieldConfig($this->typeRegistry);
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

    /**
     * Get a named type from the test FieldTypeRegistry.
     */
    protected function getType($name)
    {
        return $this->typeRegistry->getType($name);
    }

    /**
     * Return the result of rendering a field in the list context.
     *
     * You should have added the field with $this->config->add().
     */
    protected function listContext($entity, $field)
    {
        return $this->renderer->listContext($entity, $field, $this->config->getTypes(CrudRequest::CONTEXT_LIST)[$field]);
    }

    /**
     * Return the result of rendering a field in the view context.
     *
     * You should have added the field with $this->config->add().
     */
    protected function viewContext($entity, $field)
    {
        return $this->renderer->viewContext($entity, $field, $this->config->getTypes(CrudRequest::CONTEXT_VIEW)[$field]);
    }
}
