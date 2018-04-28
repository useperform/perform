<?php

namespace Perform\SpamBundle\Checker;

use Perform\SpamBundle\Entity\Report;

/**
 * The result of calling a check*() method on the SpamManager.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CheckResult
{
    protected $reports = [];

    public function addReport(Report $report)
    {
        $this->reports[] = $report;
    }

    public function getReports()
    {
        return $this->reports;
    }

    public function isSpam()
    {
        return count($this->reports) > 0;
    }

    /**
     * Merge the reports from another check result into this one.
     *
     * @param CheckResult $checkResult
     *
     * @return self
     */
    public function mergeReports(CheckResult $checkResult)
    {
        $this->reports = array_merge($this->reports, $checkResult->getReports());

        return $this;
    }
}
