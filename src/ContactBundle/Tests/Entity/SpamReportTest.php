<?php

namespace Perform\ContactBundle\Tests\Entity;

use Perform\ContactBundle\Entity\SpamReport;
use Symfony\Component\HttpFoundation\Request;

/**
 * SpamReportTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SpamReportTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateFromRequest()
    {
        $request = new Request([], [], []);
        $request->server->set('REMOTE_ADDR', '127.0.0.1');
        $request->headers->set('User-Agent', 'curl 1.0');

        $report = SpamReport::createFromRequest($request);

        $this->assertInstanceOf(SpamReport::class, $report);
        $this->assertSame('127.0.0.1', $report->getIp());
        $this->assertSame('curl 1.0', $report->getUserAgent());
    }

    public function testCreateFromRequestNoUserAgent()
    {
        $request = new Request();
        $report = SpamReport::createFromRequest($request);

        $this->assertInstanceOf(SpamReport::class, $report);
        $this->assertSame('Unknown', $report->getIp());
        $this->assertSame('Unknown', $report->getUserAgent());
    }
}
