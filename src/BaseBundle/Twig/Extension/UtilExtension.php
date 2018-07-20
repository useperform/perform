<?php

namespace Perform\BaseBundle\Twig\Extension;

use Carbon\Carbon;

/**
 * General twig helpers.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UtilExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('perform_human_date', [$this, 'humanDate']),
        ];
    }

    public function humanDate(\DateTime $date = null)
    {
        if (!$date) {
            return '';
        }

        return Carbon::instance($date)->diffForHumans();
    }

    public function getName()
    {
        return 'perform_base_util';
    }
}
