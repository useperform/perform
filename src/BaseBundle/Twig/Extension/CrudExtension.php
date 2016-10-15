<?php

namespace Perform\BaseBundle\Twig\Extension;

use Perform\BaseBundle\Routing\CrudUrlGenerator;
use Perform\BaseBundle\Util\StringUtil;
use Perform\BaseBundle\Type\TypeRegistry;
use Carbon\Carbon;

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
            new \Twig_SimpleFunction('perform_crud_route', [$this->urlGenerator, 'generate']),
            new \Twig_SimpleFunction('perform_crud_list_context', [$this, 'listContext']),
            new \Twig_SimpleFunction('perform_crud_view_context', [$this, 'viewContext']),
        ];
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('human_date', [$this, 'humanDate']),
        ];
    }

    public function listContext($entity, $field, array $config)
    {
        return $this->typeRegistry->getType($config['type'])->listContext($entity, $field, $config['options']);
    }

    public function viewContext($entity, $field, array $config)
    {
        return $this->typeRegistry->getType($config['type'])->viewContext($entity, $field, $config['options']);
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
        return 'crud';
    }
}
