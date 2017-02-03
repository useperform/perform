<?php

namespace Perform\BaseBundle\Filter;

/**
 * Filter
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Filter
{
    protected $config;
    protected $count;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param int $count
     */
    public function setCount($count)
    {
        $this->count = (int) $count;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    public function hasCount()
    {
        return null !== $this->count;
    }
}
