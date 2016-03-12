<?php

namespace Admin\Base\Twig\Extension;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Admin\Base\Admin\AdminRegistry;

/**
 * CrudExtension.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudExtension extends \Twig_Extension
{
    protected $adminRegistry;
    protected $urlGenerator;

    public function __construct(AdminRegistry $adminRegistry, UrlGeneratorInterface $urlGenerator)
    {
        $this->adminRegistry = $adminRegistry;
        $this->urlGenerator = $urlGenerator;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('sensible', [$this, 'sensible']),
            new \Twig_SimpleFunction('crud_route', [$this, 'crudRoute']),
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

    /**
     * Get the url to a crud action for an entity.
     *
     * @param mixed $entity
     * @param string $action
     *
     * @return string
     */
    public function crudRoute($entity, $action)
    {
        $params = $action === 'list' ? [] : ['id' => $entity->getId()];
        $prefix = rtrim($this->adminRegistry->getAdminForEntity($entity)->getRoutePrefix(), '_');

        return $this->urlGenerator->generate($prefix.'_'.$action, $params);
    }

    public function getName()
    {
        return 'crud';
    }
}
