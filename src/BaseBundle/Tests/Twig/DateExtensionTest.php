<?php

namespace Perform\BaseBundle\Tests\Twig;

use PHPUnit\Framework\TestCase;
use Perform\BaseBundle\Twig\Extension\DateExtension;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DateExtensionTest extends TestCase
{
    protected $extension;

    public function setUp()
    {
        $this->extension = new DateExtension();
    }

    public function testHumanDateNoDate()
    {
        $this->assertSame('', $this->extension->humanDate(null));
    }
}
