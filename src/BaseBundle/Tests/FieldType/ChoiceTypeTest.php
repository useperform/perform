<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\UserBundle\Entity\User;
use Perform\BaseBundle\FieldType\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType as ChoiceFormType;
use Perform\BaseBundle\Test\FieldTypeTestCase;
use Perform\BaseBundle\Test\WhitespaceAssertions;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ChoiceTypeTest extends FieldTypeTestCase
{
    use WhitespaceAssertions;

    protected $type;

    public function registerTypes()
    {
        return [
            'choice' => new ChoiceType(),
        ];
    }

    public function testCreateContext()
    {
        $choices = [
            'Foo' => 'foo',
            'Bar' => 'bar',
        ];
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->expects($this->once())
            ->method('add')
            ->with('field', ChoiceFormType::class, [
                'choices' => $choices,
                'label' => 'Choice Label',
            ]);

        $vars = $this->getType('choice')->createContext($builder, 'field', [
            'choices' => $choices,
            'label' => 'Choice Label',
            'form_options' => [],
        ]);
        $this->assertSame([], $vars);
    }

    public function testListContext()
    {
        $obj = new \stdClass();
        $obj->field = 'bar';
        $this->config->add('field', [
            'type' => 'choice',
            'options' => [
                'choices' => [
                    'Foo' => 'foo',
                    'Bar' => 'bar',
                ],
            ],
        ]);

        $this->assertTrimmedString('Bar', $this->listContext($obj, 'field'));
    }

    public function testListContextShowValue()
    {
        $obj = new \stdClass();
        $obj->field = 'bar';
        $this->config->add('field', [
            'type' => 'choice',
            'options' => [
                'choices' => [
                    'Foo' => 'foo',
                    'Bar' => 'bar',
                ],
                'show_label' => false,
            ],
        ]);

        $this->assertTrimmedString('bar', $this->listContext($obj, 'field'));
    }

    public function testListContextUnknownValue()
    {
        $obj = new \stdClass();
        $obj->field = 'quo';
        $this->config->add('field', [
            'type' => 'choice',
            'options' => [
                'choices' => [
                    'Foo' => 'foo',
                    'Bar' => 'bar',
                ],
                'unknown_label' => 'Unknown',
            ],
        ]);

        $this->assertTrimmedString('Unknown', $this->listContext($obj, 'field'));
    }
}
