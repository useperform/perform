<?php

namespace Perform\Licensing\Exception;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LicensingException extends \Exception
{
    public static function badJson()
    {
        return new self('An error occurred decoding the response from the licensing server.');
    }

    public static function badCurl($curlError)
    {
        return new self(sprintf('An error occurred checking the project key with the licensing server: %s', $curlError));
    }

    public static function invalidResponse()
    {
        return new self('The response from the licensing server is invalid.');
    }
}
