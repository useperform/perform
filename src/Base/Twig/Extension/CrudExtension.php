<?php

namespace Admin\Base\Twig\Extension;

use Admin\Base\Routing\CrudUrlGenerator;
use Admin\Base\Util\StringUtils;

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
            new \Twig_SimpleFunction('crud_label', [$this, 'label']),
            new \Twig_SimpleFunction('crud_route', [$this->urlGenerator, 'generate']),
        ];
    }

    /**
     * Get the configured label for a field from supplied options, or
     * create a sensible label if not configured.
     *
     * @param string $field
     * @param array  $options
     *
     * @return string
     */
    public function label($field, array $options)
    {
        if (isset($options['label'])) {
            return $options['label'];
        }

        return StringUtils::sensible($field);
    }

    public function getName()
    {
        return 'crud';
    }
}
