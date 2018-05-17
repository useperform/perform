<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\Type\SlugType;
use Perform\BaseBundle\Asset\AssetContainer;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SlugTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $assets;
    protected $type;

    public function setUp()
    {
        $this->assets = new AssetContainer();
        $this->type = new SlugType($this->assets);
    }

    public function testDefaultCreateContextVars()
    {
        $builder = $this->getMock(FormBuilderInterface::class);
        $builder->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('crud_form'));
        $config = [
            'label' => 'Slug',
            'target' => 'title',
            'readonly' => true,
        ];
        $expected = [
            'readonly' => true,
            'target' => '#crud_form_title',
        ];

        $this->assertEquals($expected, $this->type->createContext($builder, 'slug', $config));
    }

    public function testCreateContextVars()
    {
        $builder = $this->getMock(FormBuilderInterface::class);
        $builder->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('crud_form'));
        $config = [
            'label' => 'Slug',
            'target' => 'title',
            'readonly' => true,
        ];
        $expected = [
            'readonly' => true,
            'target' => '#crud_form_title',
        ];

        $this->assertEquals($expected, $this->type->createContext($builder, 'slug', $config));
    }
}
