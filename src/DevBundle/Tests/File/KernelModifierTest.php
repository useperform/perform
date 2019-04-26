<?php

namespace Perform\DevBundle\Tests\File;

use PHPUnit\Framework\TestCase;
use Perform\DevBundle\File\KernelModifier;

/**
 * KernelModifierTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 * @group kernel
 **/
class KernelModifierTest extends TestCase
{
    public function setUp()
    {
        copy(__DIR__.'/../Fixtures/NonModifiedKernel.php', __DIR__.'/../Fixtures/TestKernel.php');
        require_once(__DIR__.'/../Fixtures/TestKernel.php');
    }

    public function tearDown()
    {
        unlink(__DIR__.'/../Fixtures/TestKernel.php');
    }

    public function testAddBundle()
    {
        $this->modifier = new KernelModifier(new \TestKernel('test', false));
        $this->modifier->addBundle('Site\FrontendBundle\SiteFrontendBundle');
        $this->assertFileEquals(__DIR__.'/../Fixtures/ModifiedKernel.php', __DIR__.'/../Fixtures/TestKernel.php');
    }

    public function testBundleIsNotAddedMoreThanOnce()
    {
        $this->modifier = new KernelModifier(new \TestKernel('test', false));
        $this->modifier->addBundle('Site\FrontendBundle\SiteFrontendBundle');
        $this->modifier->addBundle('Site\FrontendBundle\SiteFrontendBundle');
        $this->modifier->addBundle('Site\FrontendBundle\SiteFrontendBundle');
        $this->modifier->addBundle('Site\FrontendBundle\SiteFrontendBundle');
        $this->assertFileEquals(__DIR__.'/../Fixtures/ModifiedKernel.php', __DIR__.'/../Fixtures/TestKernel.php');
    }
}
