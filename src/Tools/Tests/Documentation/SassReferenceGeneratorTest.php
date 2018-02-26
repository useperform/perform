<?php

namespace Perform\Tools\Tests\Documentation;

use Temping\Temping;
use Perform\Tools\Documentation\SassReferenceGenerator;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SassReferenceGeneratorTest extends \PHPUnit_Framework_TestCase
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
        $this->gen = new SassReferenceGenerator($twig);
    }

    public function tearDown()
    {
        $this->temp->reset();
    }

    public function testGenerateFile()
    {
        $file = $this->temp->getPathname('sass_ref.rst');
        $this->gen->generateFile(__DIR__.'/sample_vars.scss', $file);
        $this->assertFileEquals(__DIR__.'/sass_ref_output.rst', $file);
    }
}
