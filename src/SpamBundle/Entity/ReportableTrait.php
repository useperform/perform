<?php

namespace Perform\SpamBundle\Entity;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
trait ReportableTrait
{
    protected $spamReports;

    /**
     * @return Collection
     */
    public function getSpamReports()
    {
        return $this->spamReports;
    }

    /**
     * @param Report $spamReport
     *
     * @return self
     */
    public function addSpamReport(Report $spamReport)
    {
        $this->spamReports[] = $spamReport;

        return $this;
    }

    /**
     * @param Report $spamReport
     *
     * @return self
     */
    public function removeSpamReport(Report $report)
    {
        $this->spamReports->removeElement($report);

        return $this;
    }
}
