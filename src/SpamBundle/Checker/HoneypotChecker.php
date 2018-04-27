<?php

namespace Perform\SpamBundle\Checker;

use Symfony\Component\Form\FormInterface;
use Perform\SpamBundle\Event\HoneypotEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Perform\SpamBundle\Entity\Report;

/**
 * Listens to honeypot form fields being filled out, then marks those
 * forms as spam.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class HoneypotChecker implements FormCheckerInterface
{
    const REPORT_TYPE = 'honeypot';

    protected $requestStack;
    protected $caughtForms = [];

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function onHoneypotCaught(HoneypotEvent $event)
    {
        $this->caughtForms[] = $event->getForm();
        $this->caughtForms[] = $event->getRootForm();
    }

    public function checkForm(CheckResult $result, FormInterface $form)
    {
        if (in_array($form, $this->caughtForms, true)) {
            $report = new Report();
            $report->setType(self::REPORT_TYPE);
            $report->addRequestDetails($this->requestStack->getCurrentRequest());
            $result->addReport($report);
        }
    }
}
