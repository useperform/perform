<?php

namespace Perform\BaseBundle\Tests\Routing;

use Perform\BaseBundle\Routing\CrudUrlGenerator;
use Perform\BaseBundle\Entity\User;
use Perform\BaseBundle\Admin\UserAdmin;

/**
 * CrudUrlGeneratorTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudUrlGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected $adminRegistry;
    protected $urlGenerator;
    protected $generator;

    public function setUp()
    {
        $this->adminRegistry = $this->getMockBuilder('Perform\BaseBundle\Admin\AdminRegistry')
                             ->disableOriginalConstructor()
                             ->getMock();
        $this->urlGenerator = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $this->generator = new CrudUrlGenerator($this->adminRegistry, $this->urlGenerator);
    }

    public function testGenerateList()
    {
        $user = new User();
        $this->adminRegistry->expects($this->any())
            ->method('getAdminForEntity')
            ->with($user)
            ->will($this->returnValue(new UserAdmin()));
        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->with('perform_base_user_list')
            ->will($this->returnValue('/admin/users'));

        $this->assertSame('/admin/users', $this->generator->generate($user, 'list'));
    }

    public function testGenerateListWithString()
    {
        $this->adminRegistry->expects($this->any())
            ->method('getAdmin')
            ->with('PerformBaseBundle:User')
            ->will($this->returnValue(new UserAdmin()));
        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->with('perform_base_user_list')
            ->will($this->returnValue('/admin/users'));

        $this->assertSame('/admin/users', $this->generator->generate('PerformBaseBundle:User', 'list'));
    }

    public function testGenerateView()
    {
        $user = $this->getMock('Perform\BaseBundle\Entity\User');
        $user->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->adminRegistry->expects($this->any())
            ->method('getAdminForEntity')
            ->with($user)
            ->will($this->returnValue(new UserAdmin()));
        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->with('perform_base_user_view', ['id' => 1])
            ->will($this->returnValue('/admin/users/view/1'));

        $this->assertSame('/admin/users/view/1', $this->generator->generate($user, 'view'));
    }

    public function testGenerateEdit()
    {
        $user = $this->getMock('Perform\BaseBundle\Entity\User');
        $user->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->adminRegistry->expects($this->any())
            ->method('getAdminForEntity')
            ->with($user)
            ->will($this->returnValue(new UserAdmin()));
        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->with('perform_base_user_edit', ['id' => 1])
            ->will($this->returnValue('/admin/users/edit/1'));

        $this->assertSame('/admin/users/edit/1', $this->generator->generate($user, 'edit'));
    }

    public function testGenerateDelete()
    {
        $user = $this->getMock('Perform\BaseBundle\Entity\User');
        $user->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->adminRegistry->expects($this->any())
            ->method('getAdminForEntity')
            ->with($user)
            ->will($this->returnValue(new UserAdmin()));
        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->with('perform_base_user_delete', ['id' => 1])
            ->will($this->returnValue('/admin/users/delete/1'));

        $this->assertSame('/admin/users/delete/1', $this->generator->generate($user, 'delete'));
    }
}
