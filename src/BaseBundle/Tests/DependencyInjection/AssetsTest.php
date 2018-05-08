<?php

namespace Perform\BaseBundle\Tests\DependencyInjection;

use Perform\BaseBundle\DependencyInjection\Assets;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AssetsTest extends \PHPUnit_Framework_TestCase
{
    public function testExtraJs()
    {
        $container = new ContainerBuilder();
        Assets::addExtraJavascript($container, 'one', 'one.js');
        $this->assertSame(['one' => 'one.js'], $container->getParameter(Assets::PARAM_EXTRA_JS));

        Assets::addExtraJavascript($container, 'two', 'two.js');
        Assets::addExtraJavascript($container, 'three', 'three.js');
        $expected = [
            'one' => 'one.js',
            'two' => 'two.js',
            'three' => 'three.js'
        ];
        $this->assertSame($expected, $container->getParameter(Assets::PARAM_EXTRA_JS));
    }

    public function testExtraSass()
    {
        $container = new ContainerBuilder();
        Assets::addExtraSass($container, 'one.scss');
        $this->assertSame(['one.scss'], $container->getParameter(Assets::PARAM_EXTRA_SASS));

        Assets::addExtraSass($container, 'two.scss');
        Assets::addExtraSass($container, 'three.scss');
        $this->assertSame(['one.scss', 'two.scss', 'three.scss'], $container->getParameter(Assets::PARAM_EXTRA_SASS));
    }
}
