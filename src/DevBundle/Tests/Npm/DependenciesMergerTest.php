<?php

namespace Perform\DevBundle\Tests\Packaging;

use PHPUnit\Framework\TestCase;
use Perform\DevBundle\Npm\DependenciesMerger;
use Perform\DevBundle\Npm\PackageUpdate;
use Perform\DevBundle\Npm\PackageConflict;

class DependenciesMergerTest extends TestCase
{
    public function testCreateFromFile()
    {
        $expected = [
            "vue" => "^2.5.3",
            "bootstrap" => "^4.0.0-beta.2",
            "bootstrap-vue" => "^1.0.2",
        ];
        $merger = DependenciesMerger::createFromPackageFile(__DIR__.'/sample_package.json');
        $this->assertSame($expected, $merger->getDependencies());
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
                //expected updates
                [
                    'some-other-dep' => ['', '^0.1'],
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
    public function testMergeWithValidConstraints($existing, $new, $expected, $expectedUpdates)
    {
        $merger = new DependenciesMerger($existing);
        $merger->mergeDependencies($new, 'test');
        $this->assertSame($expected, $merger->getDependencies());

        $actualUpdates = $merger->getUpdates();
        $this->assertSame(count($expectedUpdates), count($actualUpdates));
        foreach ($expectedUpdates as $package => $expectedUpdate) {
            $actualUpdate = $actualUpdates[$package];
            $this->assertInstanceOf(PackageUpdate::class, $actualUpdate);
            $this->assertSame($expectedUpdate[0], $actualUpdate->getExistingVersion());
            $this->assertSame($expectedUpdate[1], $actualUpdate->getNewVersion());
            $this->assertSame('test', $actualUpdate->getSource());
        }

        $this->assertEmpty($merger->getConflicts());
        $this->assertFalse($merger->hasConflicts());
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
                //expected conflicts
                [
                    'some-dep' => ['^3.1.0', '^4.1'],
                ]
            ],
        ];
    }

    /**
     * @dataProvider invalidProvider
     */
    public function testMergeWithInvalidConstraints($existing, $new, $expected, $expectedConflicts)
    {
        $merger = new DependenciesMerger($existing);
        $merger->mergeDependencies($new, 'test');

        $this->assertSame($expected, $merger->getDependencies());

        $actualConflicts = $merger->getConflicts();
        $this->assertSame(count($expectedConflicts), count($actualConflicts));
        foreach ($expectedConflicts as $package => $expectedConflict) {
            $actualConflict = $actualConflicts[$package];
            $this->assertInstanceOf(PackageConflict::class, $actualConflict);
            $this->assertSame($expectedConflict[0], $actualConflict->getExistingVersion());
            $this->assertSame($expectedConflict[1], $actualConflict->getConflictingVersion());
            $this->assertSame('test', $actualConflict->getSource());
        }

        $this->assertTrue($merger->hasConflicts());
    }
}
