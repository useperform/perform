<?php

namespace Perform\BaseBundle\Asset;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ThemeNotFoundException extends \Exception
{
    public static function missing($theme)
    {
        return new self(sprintf('The theme "%s" was not found. A theme must be of the form <Bundle>:<theme_name>, e.g. "AppBundle:my_theme".', $theme));
    }

    public static function missingFile($filename, $theme)
    {
        return new self(sprintf('The file "%s" for theme "%s" was not found. Each theme must have theme.scss and variables.scss files in the Resources/scss/themes/<theme_name>/ directory.', $theme, $filename));
    }
}
