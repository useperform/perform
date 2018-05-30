<?php

namespace Perform\BaseBundle\Tests\Twig;

use Perform\BaseBundle\Twig\Extension\UtilExtension;
use Perform\BaseBundle\Config\ConfigStoreInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UtilExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $urlGenerator;
    protected $configStore;
    protected $extension;

    public function setUp()
    {
        $this->urlGenerator = $this->getMock(UrlGeneratorInterface::class);
        $this->configStore = $this->getMock(ConfigStoreInterface::class);
        $this->extension = new UtilExtension($this->urlGenerator, $this->configStore);
    }

    public function testHumanDateNoDate()
    {
        $this->assertSame('', $this->extension->humanDate(null));
    }

    public function testRouteExists()
    {
        $this->assertTrue($this->extension->routeExists('some_route'));
    }

    public function testRouteDoesNotExist()
    {
        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->with('some_route')
            ->will($this->throwException(new RouteNotFoundException()));

        $this->assertFalse($this->extension->routeExists('some_route'));
    }
}
