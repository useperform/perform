<?php

namespace Perform\SpamBundle\Tests;

use Perform\BaseBundle\Test\Services;
use Perform\SpamBundle\Checker\CheckResult;
use Perform\SpamBundle\Checker\FormCheckerInterface;
use Perform\SpamBundle\Checker\RequestCheckerInterface;
use Perform\SpamBundle\Checker\TextCheckerInterface;
use Perform\SpamBundle\SpamManager;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SpamManagerTest extends \PHPUnit_Framework_TestCase
{
    private function create(array $textCheckers, array $formCheckers, array $requestCheckers)
    {
        return new SpamManager(
            Services::serviceLocator($textCheckers),
            Services::serviceLocator($formCheckers),
            Services::serviceLocator($requestCheckers)
        );
    }

    public function testCheckText()
    {
        $manager = $this->create([
            'one' => $one = $this->getMock(TextCheckerInterface::class),
            'two' => $two = $this->getMock(TextCheckerInterface::class),
        ], [], []);
        foreach ([$one, $two] as $checker) {
            $checker->expects($this->once())
                ->method('checkText')
                ->with($this->callback(function ($result) {
                    return $result instanceof CheckResult;
                }), 'is this spam?');
        }
        $manager->checkText('is this spam?');
    }

    public function testCheckForm()
    {
        $manager = $this->create([], [
            'one' => $one = $this->getMock(FormCheckerInterface::class),
            'two' => $two = $this->getMock(FormCheckerInterface::class),
        ], []);
        $form = $this->getMock(FormInterface::class);
        foreach ([$one, $two] as $checker) {
            $checker->expects($this->once())
                ->method('checkForm')
                ->with($this->callback(function ($result) {
                    return $result instanceof CheckResult;
                }), $form);
        }
        $manager->checkForm($form);
    }

    public function testCheckRequest()
    {
        $manager = $this->create([], [], [
            'one' => $one = $this->getMock(RequestCheckerInterface::class),
            'two' => $two = $this->getMock(RequestCheckerInterface::class),
        ]);
        $request = new Request();
        foreach ([$one, $two] as $checker) {
            $checker->expects($this->once())
                ->method('checkRequest')
                ->with($this->callback(function ($result) {
                    return $result instanceof CheckResult;
                }), $request);
        }
        $manager->checkRequest($request);
    }
}
