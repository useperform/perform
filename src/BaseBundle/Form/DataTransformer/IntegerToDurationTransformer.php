<?php

namespace Perform\BaseBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Perform\BaseBundle\Util\DurationUtil;

/**
 * IntegerToDurationTransformer.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class IntegerToDurationTransformer implements DataTransformerInterface
{
    /**
     * Transform integer into human readable duration.
     */
    public function transform($value)
    {
        if ($value === null) {
            return;
        }
        if (!is_int($value)) {
            throw new UnexpectedTypeException($value, 'integer');
        }

        return DurationUtil::toHuman($value);
    }

    /**
     * Transform human readable duration into integer.
     */
    public function reverseTransform($value)
    {
        return DurationUtil::toDuration($value);
    }
}
