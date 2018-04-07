<?php

namespace Perform\PageEditorBundle\DataFixtures\ORM;

use Perform\PageEditorBundle\Entity\Version;
use Perform\PageEditorBundle\Entity\Section;
use Doctrine\Common\Persistence\ObjectManager;
use Perform\BaseBundle\DataFixtures\ORM\EntityDeclaringFixtureInterface;
use Perform\RichContentBundle\DataFixtures\FixtureManager;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LoadVersionData implements EntityDeclaringFixtureInterface
{
    protected $fixtureManager;
    protected $fixtureDefinitions = [];

    public function __construct(FixtureManager $fixtureManager, $fixtureDefinitions)
    {
        $this->fixtureManager = $fixtureManager;
        $this->fixtureDefinitions = $fixtureDefinitions;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->fixtureDefinitions as $page => $config) {
            for ($i = 0; $i < $config['versions']; ++$i) {
                $version = $this->createVersion($page, $i, $config['sections']);
                $manager->persist($version);
            }
        }

        $manager->flush();
        //publish first version?
    }

    protected function createVersion($page, $index, array $sections)
    {
        $version = new Version();
        $version->setTitle('Version number '.$index);
        $version->setPage($page);

        foreach ($sections as $sectionName => $config) {
            $section = new Section();
            $section->setName($sectionName);
            $section->setContent($this->fixtureManager->generate($config['profile']));
            $version->addSection($section);
        }

        return $version;
    }

    public function getOrder()
    {
        return 1;
    }

    public function getEntityClasses()
    {
        return [
            Version::class,
            Section::class,
        ];
    }
}
