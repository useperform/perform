<?php

namespace Perform\BaseBundle\Exporter;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface WriterFactoryInterface
{
    /**
     * Create a writer to export to $format with the given options.
     *
     * @return WriterInterface
     */
    public function create($format, array $options = []);
}
