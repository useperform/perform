<?php

namespace Admin\Base\Tests\Doctrine;

use Admin\Base\Doctrine\ExtendedYamlDriver;

/**
 * ExtendedYamlDriverTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ExtendedYamlDriverTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->driver = new ExtendedYamlDriver([], '.orm.yml', [
            'ParentBundle\Entity\Message' => 'Application\Entity\Message',
        ]);
    }

    public function testParentBecomesMappedSuperclass()
    {
        $config = [
            'ParentBundle\Entity\Message' => [
                'type' => 'entity',
            ],
        ];
        $expected = [
            'ParentBundle\Entity\Message' => [
                'type' => 'mappedSuperclass',
            ],
        ];
        $this->assertSame($expected, $this->driver->processConfig($config));
    }

    public function testParentTargetEntityIsOverridden()
    {
        $config = [
            'ParentBundle\Entity\SpamReport' => [
                'manyToOne' => [
                    'message' => [
                        'targetEntity' => 'ParentBundle\Entity\Message',
                    ]
                ]
            ],
        ];
        $expected = [
            'ParentBundle\Entity\SpamReport' => [
                'manyToOne' => [
                    'message' => [
                        'targetEntity' => 'Application\Entity\Message',
                    ]
                ]
            ],
        ];
        $this->assertSame($expected, $this->driver->processConfig($config));
    }
}
