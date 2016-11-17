<?php

namespace Perform\BaseBundle\Twig\Extension;

use Perform\BaseBundle\Routing\CrudUrlGenerator;
use Perform\BaseBundle\Util\StringUtil;
use Perform\BaseBundle\Type\TypeRegistry;
use Carbon\Carbon;
use Perform\BaseBundle\Type\TypeConfig;

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
            new \Twig_SimpleFunction('perform_crud_route_exists', [$this->urlGenerator, 'routeExists']),
            new \Twig_SimpleFunction('perform_crud_list_context', [$this, 'listContext'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('perform_crud_view_context', [$this, 'viewContext'], ['is_safe' => ['html']]),
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
        $type = $this->typeRegistry->getType($config['type']);
        $value = $type->listContext($entity, $field, $config['options']);

        if (in_array(TypeConfig::CONTEXT_LIST, $type->getHtmlContexts())) {
            return $value;
        }

        //check how twig does this
        return htmlspecialchars($value);
    }

    public function viewContext($entity, $field, array $config)
    {
        $type = $this->typeRegistry->getType($config['type']);
        $value = $type->viewContext($entity, $field, $config['options']);

        if (in_array(TypeConfig::CONTEXT_VIEW, $type->getHtmlContexts())) {
            return $value;
        }

        //check how twig does this
        return htmlspecialchars($value);
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
