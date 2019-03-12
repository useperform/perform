<?php

namespace Perform\BaseBundle\Tests\FieldType;

use Perform\BaseBundle\FieldType\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Perform\BaseBundle\Test\FieldTypeTestCase;
use Perform\BaseBundle\Test\WhitespaceAssertions;
use Money\Money;
use Perform\BaseBundle\Form\Type\MoneyType as FormType;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 * @group kernel
 **/
class MoneyTypeTest extends FieldTypeTestCase
{
    use WhitespaceAssertions;

    protected function registerTypes()
    {
        return [
            'money' => new MoneyType(),
        ];
    }

    public function testListContext()
    {
        $contact = new \stdClass();
        $contact->price = Money::GBP(10034);

        $this->config->add('price', [
            'type' => 'money',
        ]);

        $this->assertTrimmedString('£100.34', $this->listContext($contact, 'price'));
    }

    public function testCreateContext()
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->expects($this->once())
            ->method('add')
            ->with('price', FormType::class);

        $this->getType('money')->createContext($builder, 'price', ['form_options' => []]);
    }
}
