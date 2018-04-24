<?php

namespace Perform\BaseBundle\Util;

/**
 * DurationUtil.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DurationUtil
{
    /**
     * Transform seconds into a human readable string.
     *
     * @param int $duration
     *
     * @return string
     */
    public static function toHuman($duration)
    {
        $result = '';
        foreach (static::getPieces($duration) as $label => $amount) {
            if ($amount !== 0) {
                $result .= $amount.$label.' ';
            }
        }

        return trim($result);
    }

    /**
     * Transform a human readable string into seconds.
     *
     * @param string The duration, e.g. 3d 5h 30m
     *
     * @return int
     */
    public static function fromHuman($string)
    {
        $days = preg_match('`(\d+)d`', $string, $matches) ? (int) $matches[0] : 0;
        $hours = preg_match('`(\d+)h`', $string, $matches) ? (int) $matches[0] : 0;
        $minutes = preg_match('`(\d+)m`', $string, $matches) ? (int) $matches[0] : 0;
        $seconds = preg_match('`(\d+)s`', $string, $matches) ? (int) $matches[0] : 0;

        return ($days * 86400) + ($hours * 3600) + ($minutes * 60) + $seconds;
    }

    /**
     * Transform seconds into a verbose human readable string.
     *
     * @param int $duration
     *
     * @return string
     */
    public static function toVerbose($duration)
    {
        $labels = [
            'd' => ['day', 'days'],
            'h' => ['hour', 'hours'],
            'm' => ['minute', 'minutes'],
            's' => ['second', 'seconds'],
        ];

        $result = '';
        foreach (static::getPieces($duration) as $label => $amount) {
            if ($amount !== 0) {
                $result .= sprintf('%d %s ', $amount, ($amount === 1 ? $labels[$label][0] : $labels[$label][1]));
            }
        }

        return trim($result);
    }

    /**
     * Transform seconds into a digital clock style string.
     *
     * @param int $duration
     *
     * @return string
     */
    public static function toDigital($duration)
    {
        $pieces = static::getPieces($duration);

        $digits = sprintf('%s:%s:%s',
                       static::pad($pieces['h']),
                       static::pad($pieces['m']),
                       static::pad($pieces['s']));
        if ($pieces['d'] === 0) {
            return $digits;
        }

        if ($pieces['h'] !== 0 || $pieces['m'] !== 0 || $pieces['s'] !== 0) {
            return $pieces['d'].'d '.$digits;
        }

        return $pieces['d'].'d';
    }

    protected static function pad($int)
    {
        return substr('00'.(string) $int, -2);
    }

    protected static function getPieces($duration)
    {
        $duration = (int) $duration;

        return [
            'd' => (int) floor(($duration) / 86400),
            'h' => (int) floor(($duration % 86400) / 3600),
            'm' => (int) floor(($duration % 3600) / 60),
            's' => (int) $duration % 60,
        ];
    }
}
