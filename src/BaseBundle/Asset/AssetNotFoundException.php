<?php

namespace Perform\BaseBundle\Asset;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class AssetNotFoundException extends \Exception
{
    public static function invalid($name)
    {
        return new self(sprintf('Asset "%s" is invalid. An asset must be of the form <Bundle>:<name>, e.g. "AppBundle:styles.scss".', $name));
    }

    public static function missing($name)
    {
        return new self(sprintf('Asset "%s" was not found. Assets should be placed in the Resources/scss directory of the bundle.', $name));
    }
}
