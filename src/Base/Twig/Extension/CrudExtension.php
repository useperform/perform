<?php

namespace Admin\Base\Twig\Extension;

use Admin\Base\Routing\CrudUrlGenerator;
use Admin\Base\Util\StringUtils;
use Admin\Base\Type\TypeRegistry;

/**
 * CrudExtension.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudExtension extends \Twig_Extension
{
    protected $urlGenerator;
    protected $typeRegistry;

    public function __construct(CrudUrlGenerator $urlGenerator, TypeRegistry $typeRegistry)
    {
        $this->urlGenerator = $urlGenerator;
        $this->typeRegistry = $typeRegistry;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('crud_label', [$this, 'label']),
            new \Twig_SimpleFunction('crud_route', [$this->urlGenerator, 'generate']),
            new \Twig_SimpleFunction('crud_list_context', [$this, 'listContext']),
            new \Twig_SimpleFunction('crud_view_context', [$this, 'viewContext']),
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

    public function listContext($entity, $field, array $options)
    {
        return $this->typeRegistry->getType($options['type'])->listContext($entity, $field, $options);
    }

    public function viewContext($entity, $field, array $options)
    {
        return $this->typeRegistry->getType($options['type'])->viewContext($entity, $field, $options);
    }

    public function getName()
    {
        return 'crud';
    }
}
