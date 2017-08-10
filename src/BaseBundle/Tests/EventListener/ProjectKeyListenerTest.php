<?php

namespace Perform\BaseBundle\Tests\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Perform\BaseBundle\EventListener\ProjectKeyListener;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ProjectKeyListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $logger;

    public function setUp()
    {
        $this->logger = $this->getMock(LoggerInterface::class);
    }

    private function newEvent(Request $request)
    {
        return new GetResponseEvent($this->getMock(HttpKernelInterface::class), $request, HttpKernelInterface::MASTER_REQUEST);
    }

    private function newRequest($host)
    {
        $request = $this->getMock(Request::class);
        $request->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue($host));

        return $request;
    }

    private function errorFile()
    {
        return file_get_contents(__DIR__.'/../../Resources/views/Licensing/invalid.html');
    }

    public function testNoOpOnValid()
    {
        $listener = new ProjectKeyListener($this->logger, 'some-valid-key', true, ['example.com']);
        $event = $this->newEvent($this->newRequest('example.com'));
        $this->logger->expects($this->never())
            ->method('emergency');

        $listener->onKernelRequest($event);
        $this->assertNull($event->getResponse());
    }

    public function testInvalidKey()
    {
        $listener = new ProjectKeyListener($this->logger, 'some-invalid-key', false, ['example.com']);
        $event = $this->newEvent($this->newRequest('example.com'));
        $this->logger->expects($this->once())
            ->method('emergency');

        $listener->onKernelRequest($event);
        $response = $event->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertSame($this->errorFile(), $response->getContent());
    }

    public function invalidHostProvider()
    {
        return [
            [['example.co.uk']],
            [[]],
            [['example.co.uk', 'example.org']],
        ];
    }

    /**
     * @dataProvider invalidHostProvider
     */
    public function testInvalidHost(array $hosts)
    {
        $listener = new ProjectKeyListener($this->logger, 'some-valid-key', true, $hosts);
        $event = $this->newEvent($this->newRequest('example.com'));
        $this->logger->expects($this->once())
            ->method('emergency');

        $listener->onKernelRequest($event);
        $response = $event->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertSame($this->errorFile(), $response->getContent());
    }
}
