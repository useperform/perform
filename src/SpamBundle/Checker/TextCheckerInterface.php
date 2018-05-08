<?php

namespace Perform\SpamBundle\Checker;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface TextCheckerInterface
{
    public function checkText(CheckResult $result, $text);
}
