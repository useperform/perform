<?php

namespace Admin\TwitterBundle\Factory;

/**
 * FactoryInterface
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface FactoryInterface
{
    /**
     * @return Twitter
     */
    public function create();
}
