<?php

namespace Perform\BaseBundle\Tests\FieldType;

use Perform\BaseBundle\FieldType\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Perform\BaseBundle\Test\FieldTypeTestCase;
use Perform\BaseBundle\Test\WhitespaceAssertions;
use Symfony\Component\Form\Extension\Core\Type\EmailType as FormType;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EmailTypeTest extends FieldTypeTestCase
{
    use WhitespaceAssertions;

    protected function registerTypes()
    {
        return [
            'email' => new EmailType(),
        ];
    }

    public function testCreateContext()
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->expects($this->once())
            ->method('add')
            ->with('emailAddress', FormType::class);

        $this->getType('email')->createContext($builder, 'emailAddress', [
            'label' => 'Email',
            'form_options' => [],
        ]);
    }

    public function testListContext()
    {
        $contact = new \stdClass();
        $contact->emailAddress = 'contact@example.com';

        $this->config->add('emailAddress', [
            'type' => 'email',
        ]);

        $this->assertTrimmedString('<a href="mailto:contact@example.com">contact@example.com</a>', $this->listContext($contact, 'emailAddress'));
    }

    public function testViewContext()
    {
        $contact = new \stdClass();
        $contact->emailAddress = 'contact@example.com';

        $this->config->add('emailAddress', [
            'type' => 'email',
            'options' => [
                'link' => false,
            ],
        ]);

        $this->assertTrimmedString('contact@example.com', $this->viewContext($contact, 'emailAddress'));
    }

    public function testLinkIsNotShownForNullValue()
    {
        $contact = new \stdClass();
        $contact->emailAddress = null;

        $this->config->add('emailAddress', [
            'type' => 'email',
        ]);

        $this->assertTrimmedString('', $this->viewContext($contact, 'emailAddress'));
    }
}
