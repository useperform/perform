<?php

namespace Perform\BaseBundle\Tests\Admin;

use Twig\Loader\ExistsLoaderInterface;
use Twig\Environment;
use Perform\BaseBundle\Admin\AdminInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AbstractAdminTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->admin = new TestAdmin();
        $this->twig = $this->getMockBuilder(Environment::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $this->twigLoader = $this->getMock(ExistsLoaderInterface::class);
        $this->twig->expects($this->any())
            ->method('getLoader')
            ->will($this->returnValue($this->twigLoader));
    }

    public function testIsAdmin()
    {
        $this->assertInstanceOf(AdminInterface::class, $this->admin);
    }

    public function templateProvider()
    {
        return [
            ['list', '@PerformBase/crud/list.html.twig'],
            ['view', '@PerformBase/crud/view.html.twig'],
            ['create', '@PerformBase/crud/create.html.twig'],
            ['edit', '@PerformBase/crud/edit.html.twig'],
        ];
    }

    /**
     * @dataProvider templateProvider
     */
    public function testGetTemplate($context, $expected)
    {
        $this->assertSame($expected, $this->admin->getTemplate($this->twig, 'SomeBundle:SomeEntity', $context));
    }

    public function overrideTemplateProvider()
    {
        return [
            ['list', '@Some/crud/some_entity/list.html.twig'],
            ['view', '@Some/crud/some_entity/view.html.twig'],
            ['create', '@Some/crud/some_entity/create.html.twig'],
            ['edit', '@Some/crud/some_entity/edit.html.twig'],
        ];
    }

    /**
     * @dataProvider overrideTemplateProvider
     */
    public function testOverrideTemplate($context, $expected)
    {
        $this->twigLoader->expects($this->any())
            ->method('exists')
            ->with($expected)
            ->will($this->returnValue(true));

        $this->assertSame($expected, $this->admin->getTemplate($this->twig, 'SomeBundle:SomeEntity', $context));
    }
}
