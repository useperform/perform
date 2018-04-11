<?php

namespace Perform\PageEditorBundle\Tests;

use Perform\PageEditorBundle\SessionManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SessionManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $manager;
    protected $session;

    public function setUp()
    {
        $this->manager = new SessionManager();
        $this->session = $this->getMock(SessionInterface::class);
    }

    public function testStart()
    {
        $this->session->expects($this->once())
            ->method('set')
            ->with(SessionManager::SESSION_KEY);
        $this->manager->start($this->session);
    }

    public function testStop()
    {
        $this->session->expects($this->once())
            ->method('remove')
            ->with(SessionManager::SESSION_KEY);
        $this->manager->stop($this->session);
    }

    public function testNotEditing()
    {
        $this->assertFalse($this->manager->isEditing($this->session));
    }

    public function testNotEditingNoSession()
    {
        $this->assertFalse($this->manager->isEditing());
    }

    private function sessionExpects($value)
    {
        $this->session->expects($this->any())
            ->method('get')
            ->with(SessionManager::SESSION_KEY)
            ->will($this->returnValue($value));
    }

    private function createRequest($url = '/')
    {
        $request = Request::create($url);
        $request->setSession($this->session);

        return $request;
    }

    public function testIsEditing()
    {
        $this->sessionExpects(true);
        $this->assertTrue($this->manager->isEditing($this->session));
    }

    public function testRequestIsEditing()
    {
        $this->sessionExpects(true);
        $this->assertTrue($this->manager->requestIsEditing($this->createRequest()));
    }

    public function testNotEditingOnXmlHttpRequest()
    {
        $this->sessionExpects(true);
        $request = $this->createRequest();
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $this->assertFalse($this->manager->requestIsEditing($request));
    }

    public function excludedUrls()
    {
        return [
            ['/admin', ['/admin']],
            ['/admin/foo', ['^/admin']],
            ['/_profiler/123456', ['^/(_(profiler|wdt)|css|images|js)/']],
        ];
    }

    /**
     * @dataProvider excludedUrls
     */
    public function testNotEditingForExcludedUrls($url, array $regexes)
    {
        $this->sessionExpects(true);
        $request = $this->createRequest($url);
        $manager = new SessionManager($regexes);
        $this->assertFalse($manager->requestIsEditing($request));
    }
}
