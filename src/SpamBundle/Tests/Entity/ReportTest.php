<?php

namespace Perform\SpamBundle\Tests\Entity;

use Perform\SpamBundle\Entity\Report;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ReportTest extends \PHPUnit_Framework_TestCase
{
    public function testAddRequestDetails()
    {
        $report = new Report();

        $request = new Request();
        $request->server->set('REMOTE_ADDR', '192.168.0.4');
        $request->headers->set('User-Agent', 'curl 1.0');
        $report->addRequestDetails($request);

        $this->assertSame('192.168.0.4', $report->getIp());
        $this->assertSame('curl 1.0', $report->getUserAgent());
    }

    public function testEmptyRequestDetailsDoesNotOverrideExisting()
    {
        $report = new Report();
        $report->setIp('127.0.0.1');
        $report->setUserAgent('HTTPlug');

        $report->addRequestDetails(new Request());

        $this->assertSame('127.0.0.1', $report->getIp());
        $this->assertSame('HTTPlug', $report->getUserAgent());
    }

    public function testNullIsHandled()
    {
        $report = new Report();
        $report->addRequestDetails(null);
    }
}
