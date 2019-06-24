<?php

namespace Perform\SpamBundle\Tests\Checker;

use PHPUnit\Framework\TestCase;
use Perform\SpamBundle\Entity\Report;
use Perform\SpamBundle\Checker\CheckResult;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CheckResultTest extends TestCase
{
    public function testNotSpamByDefault()
    {
        $result = new CheckResult();

        $this->assertFalse($result->isSpam());
    }

    public function testIsSpam()
    {
        $result = new CheckResult();
        $result->addReport(new Report());

        $this->assertTrue($result->isSpam());
    }

    public function testMergeReports()
    {
        $result1 = new CheckResult();
        $result1->addReport($report1 = new Report());

        $result2 = new CheckResult();
        $result2->addReport($report2 = new Report());

        $result1->mergeReports($result2);

        $this->assertSame([$report1, $report2], $result1->getReports());
        $this->assertSame([$report2], $result2->getReports());
    }
}
