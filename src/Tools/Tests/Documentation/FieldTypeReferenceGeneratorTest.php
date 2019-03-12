<?php

namespace Perform\Tools\Tests\Documentation;

use PHPUnit\Framework\TestCase;
use Perform\Tools\Documentation\FieldTypeReferenceGenerator;
use Temping\Temping;
use Perform\BaseBundle\FieldType\FieldTypeRegistry;
use Perform\BaseBundle\Test\Services;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FieldTypeReferenceGeneratorTest extends TestCase
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

        $registry = Services::fieldTypeRegistry([
            'doctest' => new DocTestType(),
        ]);
        $this->gen = new FieldTypeReferenceGenerator($twig, $registry);
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
