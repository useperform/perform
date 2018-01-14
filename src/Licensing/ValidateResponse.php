<?php

namespace Perform\Licensing;

use Perform\Licensing\Exception\LicensingException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ValidateResponse
{
    protected $valid;
    protected $domains = [];

    public function __construct(array $response)
    {
        if (!isset($response['valid'])) {
            throw LicensingException::invalidResponse($response);
        }

        $this->domains = isset($response['domains']) ? (array) $response['domains'] : [];
        $this->valid = (bool) $response['valid'];
    }

    /**
     * @return array
     */
    public function getDomains()
    {
        return $this->domains;
    }

    public function isValid()
    {
        return $this->valid;
    }
}
