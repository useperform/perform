<?php

namespace Perform\BaseBundle\Tests\Config;

use Perform\BaseBundle\Action\LinkAction;
use Perform\BaseBundle\Admin\AdminRequest;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LinkActionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->action = new LinkAction();
    }

    public function testRunIsForbidden()
    {
        $this->setExpectedException(\RuntimeException::class);
        $this->action->run([], []);
    }

    public function testBatchActionDisabled()
    {
        $request = $this->getMockBuilder(AdminRequest::class)
                 ->disableOriginalConstructor()
                 ->getMock();
        $this->assertFalse($this->action->isAvailable($request));
    }

    public function testIsAlwaysGranted()
    {
        $this->assertTrue($this->action->isGranted(new \stdClass()));
    }
}
