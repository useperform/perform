<?php

namespace Perform\SpamBundle\Checker;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface RequestCheckerInterface
{
    public function checkRequest(CheckResult $result, Request $request);
}
