<?php

namespace Perform\BaseBundle\Licensing;

use Perform\BaseBundle\Exception\LicensingException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class KeyChecker
{
    protected $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function validate($key)
    {
        $c = curl_init($this->url);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, json_encode(['project_key' => $key]));
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_FAILONERROR, true);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, true);

        $json = curl_exec($c);
        if (curl_errno($c)) {
            throw LicensingException::badCurl(curl_error($c));
        }
        curl_close($c);

        $result = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($result)) {
            throw LicensingException::badJson();
        }

        return new ValidateResponse($result);
    }
}
