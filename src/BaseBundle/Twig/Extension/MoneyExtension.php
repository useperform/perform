<?php

namespace Perform\BaseBundle\Twig\Extension;

use Money\Money;
use Money\MoneyFormatter;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MoneyExtension extends \Twig_Extension
{
    protected $formatter;

    public function __construct(MoneyFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('perform_base_money', [$this, 'format']),
        ];
    }

    public function format(Money $money = null)
    {
        if (!$money) {
            return '';
        }

        try {
            return $this->formatter->format($money);
        } catch (\Exception $e) {
            return '';
        }
    }
}
