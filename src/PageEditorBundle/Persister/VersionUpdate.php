<?php

namespace Perform\PageEditorBundle\Persister;

use Perform\PageEditorBundle\Entity\Version;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class VersionUpdate
{
    public function __construct(Version $version, array $sectionNames, array $contentOperations)
    {
        $this->version = $version;
        $this->sectionNames = $sectionNames;
        $this->contentOperations = $contentOperations;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getSectionNames()
    {
        return $this->sectionNames;
    }

    public function getContentOperations()
    {
        return $this->contentOperations;
    }
}
