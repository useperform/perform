<?php

namespace Perform\BaseBundle\Licensing;

use Perform\BaseBundle\Exception\LicensingException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class KeyChecker
{
    protected $url;
    protected $bundleNames = [];

    public function __construct($url, array $bundleNames)
    {
        $this->url = $url;
        $this->bundleNames = $bundleNames;
    }

    public function validate($key)
    {
        $data = [
            'project_key' => $key,
            'os' => PHP_OS,
            'php_version' => PHP_VERSION,
            'extensions' => get_loaded_extensions(),
            'timezone' => date_default_timezone_get(),
            'bundles' => $this->bundleNames,
        ];

        $c = curl_init($this->url);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($data));
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
