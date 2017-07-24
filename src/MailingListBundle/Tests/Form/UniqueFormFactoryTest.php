<?php

namespace Perform\MailingListBundle\Tests\Form;

use Perform\MailingListBundle\Form\UniqueFormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UniqueFormFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $factory;
    protected $uniqueFactory;

    public function setUp()
    {
        $this->factory = $this->getMock(FormFactoryInterface::class);
        $this->uniqueFactory = new UniqueFormFactory($this->factory);
    }

    public function actionProvider()
    {
        return [
            ['/', 'email_only__'],
            ['/lists/beta', 'email_only__lists_beta'],
            ['/lists/beta/', 'email_only__lists_beta'],
            ['/Lists/Beta', 'email_only__lists_beta'],
            ['/mailing-list?utm=1', 'email_only__mailing_list'],
        ];
    }

    /**
     * @dataProvider actionProvider
     */
    public function testCreateGeneratesSuitableName($action, $expectedName)
    {
        $this->uniqueFactory->addType('email_only', 'SomeFormType');
        $mockForm = $this->getMock(FormInterface::class);
        $this->factory->expects($this->once())
            ->method('createNamed')
            ->with($expectedName, 'SomeFormType')
            ->will($this->returnValue($mockForm));

        $form = $this->uniqueFactory->create('email_only', $action);
        $this->assertSame($mockForm, $form);
    }

    public function testSameFormIsReturnedWithIdenticalActions()
    {
        $this->uniqueFactory->addType('email_only', 'SomeFormType');
        $mockForm = $this->getMock(FormInterface::class);
        $this->factory->expects($this->any())
            ->method('createNamed')
            ->will($this->returnValue($mockForm));

        $this->assertSame($this->uniqueFactory->create('email_only', '/submit'), $this->uniqueFactory->create('email_only', '/submit'));
    }

    public function testUnknownTypeThrowsException()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->uniqueFactory->create('some_type', '/submit');
    }
}
