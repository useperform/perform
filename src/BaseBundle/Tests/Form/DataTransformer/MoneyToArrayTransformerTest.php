<?php

namespace Perform\BaseBundle\Tests\Form\DataTransformer;

use PHPUnit\Framework\TestCase;
use Money\Money;
use Money\Currency;
use Perform\BaseBundle\Form\DataTransformer\MoneyToArrayTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MoneyToArrayTransformerTest extends TestCase
{
    public function testTransform()
    {
        $trans = new MoneyToArrayTransformer(['GBP'], 'GBP');
        $money = new Money(3400, new Currency('GBP'));
        $expected = [
            'amount' => '34.00',
            'currency' => 'GBP',
        ];
        $this->assertEquals($expected, $trans->transform($money));
    }

    public function testTransformUnknownCurrencyUsesDefault()
    {
        $trans = new MoneyToArrayTransformer(['GBP', 'EUR'], 'EUR');
        $money = new Money(1000, new Currency('USD'));
        $expected = [
            'amount' => '10.00',
            'currency' => 'EUR',
        ];
        $this->assertEquals($expected, $trans->transform($money));
    }

    public function testReverseTransform()
    {
        $trans = new MoneyToArrayTransformer(['EUR', 'USD'], 'USD');
        $data = [
            'amount' => '188.37',
            'currency' => 'EUR',
        ];
        $expected = new Money(18837, new Currency('EUR'));
        $this->assertEquals($expected, $trans->reverseTransform($data));
    }

    public function testReverseTransformUnknownCurrencyThrowsException()
    {
        $trans = new MoneyToArrayTransformer(['EUR', 'USD'], 'USD');
        $data = [
            'amount' => '188.37',
            'currency' => 'GBP',
        ];
        $this->expectException(TransformationFailedException::class);
        $trans->reverseTransform($data);
    }
}
