<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\FieldType\SlugType;
use Perform\BaseBundle\Asset\AssetContainer;
use Symfony\Component\Form\FormBuilderInterface;
use Perform\BaseBundle\Test\FieldTypeTestCase;
use Perform\BaseBundle\Test\WhitespaceAssertions;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 * @group kernel
 **/
class SlugTypeTest extends FieldTypeTestCase
{
    use WhitespaceAssertions;

    protected $assets;

    public function registerTypes()
    {
        $this->assets = new AssetContainer();

        return [
            'slug' => new SlugType($this->assets),
        ];
    }

    public function testViewContext()
    {
        $entity = new \stdClass();
        $entity->title = 'Some Title';
        $entity->slug = 'some-title';

        $this->config->add('slug', [
            'type' => 'slug',
            'options' => [
                'target' => 'title',
            ],
        ]);
        $this->assertTrimmedString('some-title', $this->viewContext($entity, 'slug'));
    }

    public function testDefaultCreateContextVars()
    {
        $builder = $this->createMock(FormBuilderInterface::class);
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

        $this->assertEquals($expected, $this->getType('slug')->createContext($builder, 'slug', $config));
    }

    public function testCreateContextVars()
    {
        $builder = $this->createMock(FormBuilderInterface::class);
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

        $this->assertEquals($expected, $this->getType('slug')->createContext($builder, 'slug', $config));
    }
}
