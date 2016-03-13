<?php

namespace Admin\MediaBundle\Tests\Entity;

use Admin\MediaBundle\Entity\File;

/**
 * FileTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FileTest extends \PHPUnit_Framework_TestCase
{
    public function testHasType()
    {
        $file = new File();
        $this->assertFalse($file->hasType());
        $file->setType('pdf');
        $this->assertTrue($file->hasType());
    }
}
