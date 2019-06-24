<?php

namespace Perform\SpamBundle\Tests;

use PHPUnit\Framework\TestCase;
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
class SpamManagerTest extends TestCase
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
            'one' => $one = $this->createMock(TextCheckerInterface::class),
            'two' => $two = $this->createMock(TextCheckerInterface::class),
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
            'one' => $one = $this->createMock(FormCheckerInterface::class),
            'two' => $two = $this->createMock(FormCheckerInterface::class),
        ], []);
        $form = $this->createMock(FormInterface::class);
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
            'one' => $one = $this->createMock(RequestCheckerInterface::class),
            'two' => $two = $this->createMock(RequestCheckerInterface::class),
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
