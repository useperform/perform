<?php

namespace Perform\Tools\Tests\Documentation;

use Perform\Tools\Documentation\DocGenerator;
use Temping\Temping;
use Perform\BaseBundle\Type\TypeRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * DocGeneratorTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DocGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected $temp;
    protected $gen;

    public function setUp()
    {
        $this->temp = new Temping();
        $this->temp->init();

        $options = [
            'strict_variables' => true,
        ];
        $twig = new \Twig_Environment($loader = new \Twig_Loader_Filesystem(__DIR__.'/../../Documentation'));

        $registry = new TypeRegistry($this->getMock(ContainerInterface::class));
        $registry->addType('doctest', DocTestType::class);

        $this->gen = new DocGenerator($twig, $registry);
    }

    public function tearDown()
    {
        $this->temp->reset();
    }

    public function testGenerateFile()
    {
        $file = $this->temp->getPathname('type_ref.rst');
        $this->gen->generateFile('doctest', $file);
        $this->assertFileEquals(__DIR__.'/type_ref_output.rst', $file);
    }
}
