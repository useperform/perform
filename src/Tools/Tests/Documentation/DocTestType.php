<?php

namespace Perform\Tools\Tests\Documentation;

use Symfony\Component\Form\FormBuilderInterface;
use Perform\BaseBundle\Type\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Here's documentation for a type.
 *
 * Documentation can continue for multiple paragraphs.
 *
 * Or even another paragraph.
 *
 * @example
 * $config->add('field', [
 *     'type' => 'doctest',
 *     'options' => [
 *         'foo' => true,
 *         'bar' => [true, 3, 'something'],
 *     ],
 * ]);
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DocTestType extends AbstractType
{
    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
    }

    /**
     * @doc foo Docs about foo.
     *
     * Something about why it's a boolean.
     *
     * @doc bar Is an array that you need to define.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['foo' => false])
            ->setAllowedTypes('foo', 'boolean')
            ->setRequired('bar')
            ->setAllowedTypes('bar', 'array');
    }
}
