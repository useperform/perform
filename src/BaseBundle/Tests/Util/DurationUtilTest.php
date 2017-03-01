<?php

namespace Perform\BaseBundle\Tests\Util;

use Perform\BaseBundle\Util\DurationUtil;

/**
 * DurationUtilTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DurationUtilTest extends \PHPUnit_Framework_TestCase
{
    public function toHumanProvider()
    {
        return [
            [30, '30s'],
            [60, '1m'],
            [120, '2m'],
            [100, '1m 40s'],
            [3600, '1h'],
            [10800, '3h'],
            [10000, '2h 46m 40s'],
            [7240, '2h 40s'],
            [10920, '3h 2m'],
            [86400, '1d'],
            [86410, '1d 10s'],
            [23872348, '276d 7h 12m 28s'],
            [864010, '10d 10s'],
            [1728120, '20d 2m'],
            [1728189, '20d 3m 9s'],
            [1731600, '20d 1h'],
            [1731601, '20d 1h 1s'],
        ];
    }

    /**
     * @dataProvider toHumanProvider
     */
    public function testToHuman($value, $expected)
    {
        $this->assertSame($expected, DurationUtil::toHuman($value));
    }
}
