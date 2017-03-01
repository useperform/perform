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
     */
    public static function toHuman($duration)
    {
        $duration = (int) $duration;

        $days = (int) floor(($duration) / 86400);
        $hours = (int) floor(($duration % 86400) / 3600);
        $minutes = (int) floor(($duration % 3600) / 60);
        $seconds = (int) $duration % 60;

        $result = '';

        $segments = [
            'd' => $days,
            'h' => $hours,
            'm' => $minutes,
            's' => $seconds,
        ];

        foreach ($segments as $label => $amount) {
            if ($amount !== 0) {
                $result .= $amount.$label.' ';
            }
        }

        return trim($result);
    }

    /**
     * @return int
     */
    public static function toDuration($string)
    {
        $days = preg_match('`(\d+)d`', $string, $matches) ? (int) $matches[0] : 0;
        $hours = preg_match('`(\d+)h`', $string, $matches) ? (int) $matches[0] : 0;
        $minutes = preg_match('`(\d+)m`', $string, $matches) ? (int) $matches[0] : 0;
        $seconds = preg_match('`(\d+)s`', $string, $matches) ? (int) $matches[0] : 0;

        return ($days * 86400) + ($hours * 3600) + ($minutes * 60) + $seconds;
    }
}
