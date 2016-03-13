<?php

namespace Admin\Base\Twig\Extension;

use Admin\Base\Routing\CrudUrlGenerator;

/**
 * CrudExtension.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudExtension extends \Twig_Extension
{
    protected $urlGenerator;

    public function __construct(CrudUrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('sensible', [$this, 'sensible']),
            new \Twig_SimpleFunction('crud_route', [$this->urlGenerator, 'generate']),
        ];
    }

    /**
     * Create a sensible, human readable default for $string,
     * e.g. creating a label for the name of form inputs.
     *
     * @param mixed  $label
     * @param string $field
     *
     * @return string
     */
    public function sensible($label, $field)
    {
        if (!is_int($label)) {
            return $label;
        }

        $string = preg_replace('`([A-Z])`', '-\1', $field);
        $string = str_replace(['-', '_'], ' ', $string);

        return ucfirst(trim(strtolower($string)));
    }

    public function getName()
    {
        return 'crud';
    }
}
