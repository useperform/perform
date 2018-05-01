<?php

namespace Perform\BaseBundle\Tests\DependencyInjection;

use Perform\BaseBundle\DependencyInjection\Assets;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AssetsTest extends \PHPUnit_Framework_TestCase
{
    public function testExtraAssetsCanBeAdded()
    {
        $container = new ContainerBuilder();
        Assets::addExtraSass($container, ['one.scss']);
        $this->assertSame(['one.scss'], $container->getParameter(Assets::PARAM_EXTRA_SASS));

        Assets::addExtraSass($container, ['two.scss', 'three.scss']);
        $this->assertSame(['one.scss', 'two.scss', 'three.scss'], $container->getParameter(Assets::PARAM_EXTRA_SASS));
    }
}
