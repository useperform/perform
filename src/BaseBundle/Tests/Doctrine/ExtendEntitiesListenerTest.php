<?php

namespace Perform\BaseBundle\Tests\Doctrine;

use Perform\BaseBundle\Doctrine\ExtendEntitiesListener;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ExtendEntitiesListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testClassesAreNormalised()
    {
        $entities = [
            'Parent\Entity\One' => 'Child\Entity\One',
            '\Parent\Entity\Two' => '\Child\Entity\Two',
        ];
        $expected = [
            'Parent\Entity\One' => 'Child\Entity\One',
            'Parent\Entity\Two' => 'Child\Entity\Two',
        ];

        $listener = new ExtendEntitiesListener($entities);
        $obj = new \ReflectionObject($listener);
        $prop = new \ReflectionProperty($listener, 'entities');
        $prop->setAccessible(true);

        $this->assertSame($expected, $prop->getValue($listener));
    }
}
