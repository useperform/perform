<?php

namespace Perform\BaseBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;
use Money\Money;
use Money\Currency;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class MoneyToArrayTransformer implements DataTransformerInterface
{
    protected $transformer;
    protected $allowedCurrencies;
    protected $defaultCurrency;

    /**
     * @param array $allowedCurrencies A list of allowed currency codes
     */
    public function __construct(array $allowedCurrencies, $defaultCurrency)
    {
        $this->transformer = new MoneyToLocalizedStringTransformer(2, false, MoneyToLocalizedStringTransformer::ROUND_HALF_UP, 100);
        $this->allowedCurrencies = $allowedCurrencies;
        $this->defaultCurrency = $defaultCurrency;
    }

    /**
     * Transform Money object to array for the form type.
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }
        if (!$value instanceof Money) {
            throw new UnexpectedTypeException($value, 'Money');
        }

        $currencyCode = $value->getCurrency()->getCode();
        if (!$this->validCurrency($currencyCode)) {
            // currency is invalid, perhaps form options have changed
            // since the entity was saved. Don't throw an exception so
            // the user has a chance to correct it in the form, just
            // change to the default currency instead.
            $currencyCode = $this->defaultCurrency;
        }

        $amount = $this->transformer->transform($value->getAmount());

        return array(
            'amount' => $amount,
            'currency' => $currencyCode,
        );
    }

    /**
     * Transform form array to Money object.
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }
        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }
        if (!isset($value['amount']) || !isset($value['currency'])) {
            return null;
        }
        if (!$this->validCurrency($currencyCode = trim($value['currency']))) {
            throw new TransformationFailedException(sprintf('The supplied currency "%s" is not in the list of allowed currencies ("%s").', $currencyCode, implode($this->allowedCurrencies, '", "')));
        }

        $amount = $this->transformer->reverseTransform($value['amount']);

        return new Money($amount, new Currency($value['currency']));
    }

    private function validCurrency($currencyCode)
    {
        return in_array($currencyCode, $this->allowedCurrencies, true);
    }
}
