<?php

namespace Perform\DevBundle\Tests\Packaging;

use PHPUnit\Framework\TestCase;
use Perform\DevBundle\Packaging\NpmMerger;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class NpmMergerTest extends TestCase
{
    public function setUp()
    {
        $this->merger = new NpmMerger();
    }

    public function testLoadRequirements()
    {
        $expected = [
            "vue" => "^2.5.3",
            "bootstrap" => "^4.0.0-beta.2",
            "bootstrap-vue" => "^1.0.2",
        ];
        $this->assertSame($expected, $this->merger->loadRequirements(__DIR__.'/sample_package.json'));
    }

    public function validProvider()
    {
        return [
            [
                //existing
                [
                    'some-dep' => '^3.6.0',
                ],
                //new
                [
                    'some-other-dep' => '^0.1',
                ],
                //expected
                [
                    'some-dep' => '^3.6.0',
                    'some-other-dep' => '^0.1',
                ],
                //expected new
                [
                    'some-other-dep' => [null, '^0.1'],
                ]
            ],

            [
                [
                    'some-dep' => '^3.6.0',
                ],
                [
                    'some-dep' => '^3.6.0',
                ],
                [
                    'some-dep' => '^3.6.0',
                ],
                [
                ],
            ],

            [
                [
                    'some-dep' => '^3.6.0',
                ],
                [
                    'some-dep' => '^3.7.0',
                ],
                [
                    'some-dep' => '^3.7.0',
                ],
                [
                    'some-dep' => ['^3.6.0', '^3.7.0'],
                ],
            ],

            [
                [
                    'some-dep' => '^3.7.0',
                ],
                [
                    'some-dep' => '^3.6.0',
                ],
                [
                    'some-dep' => '^3.7.0',
                ],
                [
                ]
            ],
        ];
    }

    /**
     * @dataProvider validProvider
     */
    public function testMergeWithValidConstraints($existing, $new, $expected, $expectedNew)
    {
        $result = $this->merger->mergeRequirements($existing, $new);
        $this->assertSame($expected, $result->getResolvedRequirements());
        $this->assertSame($expectedNew, $result->getNewRequirements());
        $this->assertEmpty($result->getUnresolvedRequirements());
    }

    public function invalidProvider()
    {
        return [
            [
                //existing
                [
                    'some-dep' => '^3.1.0',
                    'one-dep' => '^1.1.0',
                ],
                //new
                [
                    'some-dep' => '^4.1',
                    'two-dep' => '4.2.9-beta',
                ],
                //expected
                [
                    'some-dep' => '^3.1.0',
                    'one-dep' => '^1.1.0',
                    'two-dep' => '4.2.9-beta',
                ],
                //expected unresolved
                [
                    'some-dep' => ['^3.1.0', '^4.1'],
                ]
            ],
        ];
    }

    /**
     * @dataProvider invalidProvider
     */
    public function testMergeWithInvalidConstraints($existing, $new, $expected, $expectedUnresolved)
    {
        $result = $this->merger->mergeRequirements($existing, $new);
        $this->assertSame($expected, $result->getResolvedRequirements());
        $this->assertSame($expectedUnresolved, $result->getUnresolvedRequirements());
    }
}
