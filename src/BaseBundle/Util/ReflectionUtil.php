<?php

namespace Perform\BaseBundle\Util;

/**
 * Helpers for examining classes.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ReflectionUtil
{
    public static function usesTrait($classname, $traitname)
    {
        do {
            if (in_array($traitname, class_uses($classname), true)) {
                return true;
            }
        } while ($classname = get_parent_class($classname));

        return false;
    }
}
