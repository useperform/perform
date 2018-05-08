<?php

namespace Perform\SpamBundle;

use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;
use Perform\SpamBundle\Checker\CheckResult;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;

/**
 * Check various objects for signs of spam.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SpamManager
{
    protected $textCheckers;
    protected $formCheckers;
    protected $requestCheckers;

    public function __construct(
        LoopableServiceLocator $textCheckers,
        LoopableServiceLocator $formCheckers,
        LoopableServiceLocator $requestCheckers
    ) {
        $this->textCheckers = $textCheckers;
        $this->formCheckers = $formCheckers;
        $this->requestCheckers = $requestCheckers;
    }

    /**
     * @param string $text
     *
     * @return Result
     */
    public function checkText($text)
    {
        $result = new CheckResult();
        foreach ($this->textCheckers as $checker) {
            $checker->checkText($result, $text);
        }

        return $result;
    }

    /**
     * @param FormInterface $form
     *
     * @return Result
     */
    public function checkForm(FormInterface $form)
    {
        $result = new CheckResult();
        foreach ($this->formCheckers as $checker) {
            $checker->checkForm($result, $form);
        }

        return $result;
    }

    /**
     * @param Request $request
     *
     * @return Result
     */
    public function checkRequest(Request $request)
    {
        $result = new CheckResult();
        foreach ($this->requestCheckers as $checker) {
            $checker->checkRequest($result, $request);
        }

        return $result;
    }
}
