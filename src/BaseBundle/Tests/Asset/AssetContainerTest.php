<?php

namespace Perform\BaseBundle\Tests\Asset;

use Perform\BaseBundle\Asset\AssetContainer;

/**
 * AssetContainerTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AssetContainerTest extends \PHPUnit_Framework_TestCase
{
    protected $assets;

    public function setUp()
    {
        $this->assets = new AssetContainer();
    }

    public function testJs()
    {
        $this->assets->addJs('/js/foo.js');
        $this->assets->addJs('/js/bar.js');

        $this->assertSame(['/js/foo.js', '/js/bar.js'], $this->assets->getJs());
    }

    public function testDuplicateJs()
    {
        $this->assets->addJs('/js/foo.js');
        $this->assets->addJs('/js/bar.js');
        $this->assets->addJs('/js/foo.js');
        $this->assets->addJs('/js/foo.js');

        $this->assertSame(['/js/foo.js', '/js/bar.js'], $this->assets->getJs());
    }

    public function testInlineJs()
    {
        $this->assets->addInlineJs('alert("hello")');

        $this->assertSame(['alert("hello")'], $this->assets->getInlineJs());
    }

    public function testDuplicateInlineJsIsNotDetected()
    {
        $this->assets->addInlineJs('alert("hello")');
        $this->assets->addInlineJs('alert("hello")');

        $this->assertSame(['alert("hello")', 'alert("hello")'], $this->assets->getInlineJs());
    }

    public function testCss()
    {
        $this->assets->addCss('/css/foo.css');
        $this->assets->addCss('/css/bar.css');

        $this->assertSame(['/css/foo.css', '/css/bar.css'], $this->assets->getCss());
    }

    public function testDuplicateCss()
    {
        $this->assets->addCss('/css/foo.css');
        $this->assets->addCss('/css/bar.css');
        $this->assets->addCss('/css/foo.css');
        $this->assets->addCss('/css/foo.css');

        $this->assertSame(['/css/foo.css', '/css/bar.css'], $this->assets->getCss());
    }
}
