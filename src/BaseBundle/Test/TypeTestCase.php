<?php

namespace Perform\BaseBundle\Test;

use Perform\BaseBundle\Type\TypeRegistry;
use Perform\BaseBundle\Admin\ContextRenderer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Environment;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Perform\BaseBundle\Test\TestFilesystemLoader;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
abstract class TypeTestCase extends \PHPUnit_Framework_TestCase
{
    protected $typeRegistry;
    protected $renderer;

    public function setUp()
    {
        // create a kernel to get full access to the twig environment
        $this->kernel = new TestKernel();
        $this->kernel->boot();
        $twig = $this->kernel->getContainer()->get('twig');

        // but create a fresh type registry for testing
        $this->typeRegistry = new TypeRegistry($this->getMock(ContainerInterface::class));
        $this->renderer = new ContextRenderer($this->typeRegistry, $twig);
        $this->configure();
    }

    public function tearDown()
    {
        $this->kernel->shutdown();
    }

    abstract protected function configure();
}
