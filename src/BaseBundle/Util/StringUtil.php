<?php

namespace Perform\BaseBundle\Util;

/**
 * StringUtil
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class StringUtil
{
    /**
     * Create a sensible, human readable default for $string,
     * e.g. creating a label for the name of form inputs.
     *
     * @param string $string
     *
     * @return string
     */
    public static function sensible($string)
    {
        $string = preg_replace('`([A-Z])`', '-\1', $string);
        $string = str_replace(['-', '_'], ' ', $string);

        return ucfirst(trim(strtolower($string)));
    }
}