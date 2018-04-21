<?php

namespace Perform\Tools\Tests\Documentation;

use Perform\Tools\Documentation\TypeReferenceGenerator;
use Temping\Temping;
use Perform\BaseBundle\Type\TypeRegistry;
use Perform\BaseBundle\Test\Services;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TypeReferenceGeneratorTest extends \PHPUnit_Framework_TestCase
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

        $registry = Services::typeRegistry([
            'doctest' => new DocTestType(),
        ]);
        $this->gen = new TypeReferenceGenerator($twig, $registry);
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
