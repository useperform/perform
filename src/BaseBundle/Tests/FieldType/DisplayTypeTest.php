<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\FieldType\DisplayType;
use Perform\BaseBundle\Crud\CrudRequest;
use Perform\BaseBundle\Exception\InvalidFieldException;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DisplayTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $fieldType;

    public function setUp()
    {
        $this->fieldType = new DisplayType();
        $this->formBuilder = $this->getMock(FormBuilderInterface::class);
    }

    public function testDefaults()
    {
        $defaults = $this->fieldType->getDefaultConfig();
        $this->assertFalse($defaults['sort']);
        $this->assertSame([CrudRequest::CONTEXT_LIST, CrudRequest::CONTEXT_VIEW], $defaults['contexts']);
        $this->assertSame('@PerformBase/field_type/blank.html.twig', $defaults['template']);
    }

    public function testListContext()
    {
        $expected = [
            'entity' => $obj = new \stdClass(),
            'field' => 'some_field',
        ];
        $this->assertSame($expected, $this->fieldType->listContext($obj, 'some_field', ['foo' => 'bar']));
    }

    public function testViewContext()
    {
        $expected = [
            'entity' => $obj = new \stdClass(),
            'field' => 'some_field',
        ];
        $this->assertSame($expected, $this->fieldType->viewContext($obj, 'some_field', ['foo' => 'bar']));
    }

    public function testCreateContextThrowsException()
    {
        $this->setExpectedException(InvalidFieldException::class);
        $this->fieldType->createContext($this->formBuilder, 'some_field');
    }

    public function testEditContextThrowsException()
    {
        $this->setExpectedException(InvalidFieldException::class);
        $this->fieldType->editContext($this->formBuilder, 'some_field');
    }
}
