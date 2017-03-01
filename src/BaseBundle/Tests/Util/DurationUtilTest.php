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

    /**
     * @dataProvider toHumanProvider
     */
    public function testToDuration($expected, $value)
    {
        $this->assertSame($expected, DurationUtil::toDuration($value));
        //and without spaces
        $this->assertSame($expected, DurationUtil::toDuration(str_replace(' ', '', $value)));
    }

    public function toDigitalProvider()
    {
        return [
            [30, '00:00:30'],
            [60, '00:01:00'],
            [120, '00:02:00'],
            [100, '00:01:40'],
            [3600, '01:00:00'],
            [10800, '03:00:00'],
            [10000, '02:46:40'],
            [7240, '02:00:40'],
            [10920, '03:02:00'],
            [86400, '1d'],
            [86410, '1d 00:00:10'],
            [23872348, '276d 07:12:28'],
            [864010, '10d 00:00:10'],
            [1728120, '20d 00:02:00'],
            [1728189, '20d 00:03:09'],
            [1731600, '20d 01:00:00'],
            [1731601, '20d 01:00:01'],
        ];
    }

    /**
     * @dataProvider toDigitalProvider
     */
    public function testToDigital($value, $expected)
    {
        $this->assertSame($expected, DurationUtil::toDigital($value));
    }
}
