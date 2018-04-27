<?php

namespace Perform\SpamBundle\Checker;

use Symfony\Component\Form\FormInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface FormCheckerInterface
{
    public function checkForm(CheckResult $result, FormInterface $form);
}
