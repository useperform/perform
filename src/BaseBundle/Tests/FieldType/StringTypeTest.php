<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\FieldType\StringType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Perform\BaseBundle\Test\FieldTypeTestCase;
use Perform\BaseBundle\Test\WhitespaceAssertions;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class StringTypeTest extends FieldTypeTestCase
{
    use WhitespaceAssertions;

    protected function registerTypes()
    {
        return [
            'string' => new StringType(),
        ];
    }

    public function testCreateContext()
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->expects($this->once())
            ->method('add')
            ->with('forename', TextType::class);

        $this->getType('string')->createContext($builder, 'forename', [
            'label' => 'String',
            'form_options' => [],
        ]);
    }

    public function testEditContext()
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->expects($this->once())
            ->method('add')
            ->with('forename', TextType::class);

        $this->getType('string')->editContext($builder, 'forename', [
            'label' => 'String',
            'form_options' => [],
        ]);
    }

    public function testListContext()
    {
        $user = new \stdClass();
        $user->forename = 'Test';

        $this->config->add('forename', [
            'type' => 'string',
        ]);
        $this->assertTrimmedString('Test', $this->listContext($user, 'forename'));
    }
}
