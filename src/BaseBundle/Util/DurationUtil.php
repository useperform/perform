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
}
