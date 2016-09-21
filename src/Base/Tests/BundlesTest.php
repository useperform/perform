<?php

namespace Perform\Base\Tests;

/**
 * BundlesTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BundlesTest extends \PHPUnit_Framework_TestCase
{
    public function testBundlesCanBeCreated()
    {
        $names = [
            // 'Base',
            'Blog',
            'Cms',
            'Contact',
            'Events',
            'Media',
            'Notification',
            // 'Team',
            'Twitter',
        ];

        foreach ($names as $name) {
            $class = sprintf('Perform\%sBundle\Perform%sBundle', $name, $name);
            $bundle = new $class;
            $this->assertInstanceOf('Symfony\Component\HttpKernel\Bundle\BundleInterface', $bundle);
        }

    }
}
