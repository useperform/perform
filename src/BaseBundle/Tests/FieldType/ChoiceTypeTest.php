<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\UserBundle\Entity\User;
use Perform\BaseBundle\FieldType\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType as ChoiceFormType;

/**
 * ChoiceTypeTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ChoiceTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $type;

    public function setUp()
    {
        $this->type = new ChoiceType();
    }

    public function testCreateContext()
    {
        $choices = [
            'Foo' => 'foo',
            'Bar' => 'bar',
        ];
        $builder = $this->getMock(FormBuilderInterface::class);
        $builder->expects($this->once())
            ->method('add')
            ->with('field', ChoiceFormType::class, [
                'choices' => $choices,
            ]);

        $this->type->createContext($builder, 'field', ['choices' => $choices]);
    }

    public function testListContext()
    {
        $obj = new \stdClass();
        $obj->field = 'bar';
        $options = [
            'choices' => [
                'Foo' => 'foo',
                'Bar' => 'bar',
            ],
            'show_label' => true,
        ];

        $this->assertSame('Bar', $this->type->listContext($obj, 'field', $options));
    }

    public function testListContextShowValue()
    {
        $obj = new \stdClass();
        $obj->field = 'bar';
        $options = [
            'choices' => [
                'Foo' => 'foo',
                'Bar' => 'bar',
            ],
            'show_label' => false,
        ];

        $this->assertSame('bar', $this->type->listContext($obj, 'field', $options));
    }

    public function testListContextUnknownValue()
    {
        $obj = new \stdClass();
        $obj->field = 'quo';
        $options = [
            'choices' => [
                'Foo' => 'foo',
                'Bar' => 'bar',
            ],
            'show_label' => true,
            'unknown_label' => 'Unknown',
        ];

        $this->assertSame('Unknown', $this->type->listContext($obj, 'field', $options));
    }
}
