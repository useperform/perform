<?php

namespace Perform\PageEditorBundle\Tests;

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use Perform\PageEditorBundle\PageManager;
use Perform\PageEditorBundle\Repository\VersionRepository;
use Perform\RichContentBundle\Renderer\RendererInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PageManagerTest extends TestCase
{
    public function setUp()
    {
        $this->twig = $this->getMockBuilder(\Twig_Environment::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $repo = $this->getMockBuilder(VersionRepository::class)
              ->disableOriginalConstructor()
              ->getMock();
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));
        $this->renderer = $this->createMock(RendererInterface::class);
        $this->manager = new PageManager($entityManager, $this->twig, $this->renderer);
    }

    public function testEditModeDisabledByDefault()
    {
        $this->assertFalse($this->manager->inEditMode());
    }

    public function testEnabledEditMode()
    {
        $this->manager->enableEditMode();
        $this->assertTrue($this->manager->inEditMode());
    }

    public function testEditModeCanBeDisabled()
    {
        $this->manager->enableEditMode();
        $this->manager->enableEditMode(false);
        $this->assertFalse($this->manager->inEditMode());
    }
}
