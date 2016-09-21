<?php

namespace Perform\CmsBundle\Locator;

use Perform\CmsBundle\Repository\VersionRepository;

/**
 * SectionLocator
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SectionLocator
{
    protected $repo;

    public function __construct(VersionRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Find all sections for a given set of pages and section names.
     * $sections should be an array where the keys are page names, and values
     * are an array of section names.
     *
     * ['home' => ['main', 'aside'], 'about' => ['bios']]
     *
     * The sections from the current version are selected. See
     * VersionRepository#findCurrentVersion().
     *
     * Sections are returned as arrays, suitable for encoding to json.
     *
     * @param array $sections an array of pages and sections
     *
     */
    public function findCurrentSections(array $pages)
    {
        $data = [];
        foreach ($pages as $page => $sectionNames) {
            $data[$page] = [];
            $version = $this->repo->findCurrentVersion($page);
            $filteredSections = $version->getSections()->filter(function($section) use ($sectionNames) {
                return in_array($section->getName(), $sectionNames);
            });

            foreach ($filteredSections as $section) {
                $data[$page][$section->getName()] = $section->toArray();
            }
        }

        return $data;
    }
}
