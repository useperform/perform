<?php

namespace Perform\BaseBundle\Tests\Admin;

use Perform\BaseBundle\Admin\UserAdmin;
use Symfony\Component\Templating\EngineInterface;

/**
 * AbstractAdminTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AbstractAdminTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->admin = new TestAdmin();
    }

    public function testIsAdmin()
    {
        $this->assertInstanceOf('Perform\BaseBundle\Admin\AdminInterface', $this->admin);
    }

    public function templateProvider()
    {
        return [
            ['list', 'PerformBaseBundle:Crud:list.html.twig'],
            ['view', 'PerformBaseBundle:Crud:view.html.twig'],
            ['create', 'PerformBaseBundle:Crud:create.html.twig'],
            ['edit', 'PerformBaseBundle:Crud:edit.html.twig'],
        ];
    }

    /**
     * @dataProvider templateProvider
     */
    public function testGetTemplate($context, $expected)
    {
        $templating = $this->getMock(EngineInterface::class);

        $this->assertSame($expected, $this->admin->getTemplate($templating, 'SomeBundle:SomeEntity', $context));
    }

    public function overrideTemplateProvider()
    {
        return [
            ['list', 'SomeBundle:SomeEntity:list.html.twig'],
            ['view', 'SomeBundle:SomeEntity:view.html.twig'],
            ['create', 'SomeBundle:SomeEntity:create.html.twig'],
            ['edit', 'SomeBundle:SomeEntity:edit.html.twig'],
        ];
    }

    /**
     * @dataProvider overrideTemplateProvider
     */
    public function testOverrideTemplate($context, $expected)
    {
        $templating = $this->getMock(EngineInterface::class);
        $templating->expects($this->any())
            ->method('exists')
            ->with($expected)
            ->will($this->returnValue(true));

        $this->assertSame($expected, $this->admin->getTemplate($templating, 'SomeBundle:SomeEntity', $context));
    }
}
