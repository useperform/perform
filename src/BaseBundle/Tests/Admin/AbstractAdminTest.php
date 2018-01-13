<?php

namespace Perform\BaseBundle\Tests\Admin;

use Perform\UserBundle\Admin\UserAdmin;
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
        $templating = $this->getMock(EngineInterface::class);

        $this->assertSame($expected, $this->admin->getTemplate($templating, 'SomeBundle:SomeEntity', $context));
    }

    public function overrideTemplateProvider()
    {
        return [
            ['list', '@Some/admin/some_entity/list.html.twig'],
            ['view', '@Some/admin/some_entity/view.html.twig'],
            ['create', '@Some/admin/some_entity/create.html.twig'],
            ['edit', '@Some/admin/some_entity/edit.html.twig'],
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
