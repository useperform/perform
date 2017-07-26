<?php

namespace Perform\BaseBundle\Tests\Twig;

use Perform\BaseBundle\Twig\Extension\UtilExtension;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Perform\BaseBundle\Config\ConfigStoreInterface;

/**
 * UtilExtensionTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UtilExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $extension;
    protected $router;
    protected $configStore;

    public function setUp()
    {
        $this->router = $this->getMock(RouterInterface::class);
        $this->configStore = $this->getMock(ConfigStoreInterface::class);
        $this->extension = new UtilExtension($this->router, $this->configStore);
    }

    public function testHumanDateNoDate()
    {
        $this->assertSame('', $this->extension->humanDate(null));
    }

    public function testRouteExists()
    {
        $routes = new RouteCollection();
        $routes->add('bar', new Route('/bar'));
        $this->router->expects($this->any())
            ->method('getRouteCollection')
            ->will($this->returnValue($routes));

        $this->assertFalse($this->extension->routeExists('foo'));
        $this->assertTrue($this->extension->routeExists('bar'));
    }
}
