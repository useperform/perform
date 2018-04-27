<?php

namespace Perform\SpamBundle\Tests\Checker;

use Perform\SpamBundle\Entity\Report;
use Perform\SpamBundle\Checker\CheckResult;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CheckResultTest extends \PHPUnit_Framework_TestCase
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
}
