<?php

namespace Perform\BaseBundle\Tests\FieldType;

use Perform\BaseBundle\FieldType\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Perform\BaseBundle\Test\FieldTypeTestCase;
use Perform\BaseBundle\Test\WhitespaceAssertions;
use Symfony\Component\Form\Extension\Core\Type\PasswordType as FormType;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 * @group kernel
 **/
class PasswordTypeTest extends FieldTypeTestCase
{
    use WhitespaceAssertions;

    protected function registerTypes()
    {
        return [
            'password' => new PasswordType(),
        ];
    }

    public function testListContext()
    {
        $obj = new \stdClass();
        $obj->pass = 'super_secret';
        $this->config->add('pass', [
            'type' => 'password',
        ]);
        $this->assertTrimmedString('********', $this->listContext($obj, 'pass'));
    }

    public function testViewContext()
    {
        $obj = new \stdClass();
        $obj->pass = 'super_secret';
        $this->config->add('pass', [
            'type' => 'password',
        ]);
        $this->assertTrimmedString('********', $this->viewContext($obj, 'pass'));
    }

    public function testCreateContext()
    {
        $builder = $this->getMock(FormBuilderInterface::class);
        $builder->expects($this->once())
            ->method('add')
            ->with('pass', FormType::class);

        $this->getType('password')->createContext($builder, 'pass', ['label' => 'Password']);
    }
}
