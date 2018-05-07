<?php

namespace Perform\BaseBundle\Asset\Dumper;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface TargetInterface
{
    /**
     * @return string The absolute path of the target file
     */
    public function getFilename();

    /**
     * @return string The contents of the file to be dumped
     */
    public function getContents();
}
