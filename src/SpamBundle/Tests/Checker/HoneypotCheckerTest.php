<?php

namespace Perform\SpamBundle\Tests\Checker;

use PHPUnit\Framework\TestCase;
use Perform\SpamBundle\Checker\CheckResult;
use Perform\SpamBundle\Checker\HoneypotChecker;
use Perform\SpamBundle\Event\HoneypotEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class HoneypotCheckerTest extends TestCase
{
    protected $checker;
    protected $requestStack;

    public function setUp()
    {
        $this->requestStack = new RequestStack();
        $this->checker = new HoneypotChecker($this->requestStack);
    }

    public function testCheckGoodForm()
    {
        $form = $this->createMock(FormInterface::class);
        $result = new CheckResult();
        $this->checker->checkForm($result, $form);

        $this->assertSame(0, count($result->getReports()));
    }

    public function testCheckBadForm()
    {
        $form = $this->createMock(FormInterface::class);
        $this->checker->onHoneypotCaught(new HoneypotEvent($form));
        $request = new Request();
        $request->headers->set('User-Agent', 'curl');
        $this->requestStack->push($request);

        $result = new CheckResult();
        $this->checker->checkForm($result, $form);

        $this->assertSame(1, count($result->getReports()));
        $this->assertSame('honeypot', $result->getReports()[0]->getType());
        $this->assertSame('curl', $result->getReports()[0]->getUserAgent());
    }
}
