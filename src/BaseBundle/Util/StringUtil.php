<?php

namespace Perform\BaseBundle\Util;

/**
 * StringUtil.
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

    /**
     * Show the beginning of a piece of non-formatted text.
     *
     * @param string $text
     *
     * @return string
     */
    public static function preview($text)
    {
        if (strlen($text) < 50) {
            return $text;
        }

        return substr($text, 0, 50).'…';
    }

    /**
     * Create a suitable name for an entity managed by an admin class.
     *
     * @param string $class The classname of the admin
     */
    public static function adminClassToEntityName($class)
    {
        $pieces = explode('\\', $class);
        //EntityNameAdmin -> Entity Name

        return trim(preg_replace('/([A-Z][a-z])/', ' \1', substr(end($pieces), 0, -5)));
    }
}
