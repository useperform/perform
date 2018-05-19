<?php

namespace Perform\BaseBundle\Event;

use Perform\BaseBundle\Crud\CrudRequest;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ContextEvent extends Event
{
    const CONTEXT_LIST = 'perform_base.crud.list_context';
    const CONTEXT_VIEW = 'perform_base.crud.view_context';
    const CONTEXT_CREATE = 'perform_base.crud.create_context';
    const CONTEXT_EDIT = 'perform_base.crud.edit_context';

    protected $request;
    protected $templateVariables;

    /**
     * @var CrudRequest
     * @var array       $templateVariables An array of variables that will be passed to the twig template
     */
    public function __construct(CrudRequest $request, array $templateVariables)
    {
        $this->request = $request;
        $this->templateVariables = $templateVariables;
    }

    public function getCrudRequest()
    {
        return $this->request;
    }

    public function getTemplateVariables()
    {
        return $this->templateVariables;
    }

    public function setTemplateVariables(array $templateVariables)
    {
        $this->templateVariables = $templateVariables;
    }

    public function setTemplateVariable($key, $value)
    {
        $this->templateVariables[$key] = $value;
    }
}
