<?php

namespace Perform\BaseBundle\Event;

use Perform\BaseBundle\Crud\CrudRequest;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ListContextEvent extends Event
{
    const NAME = 'perform_base.list_context';

    protected $request;

    public function __construct(CrudRequest $request)
    {
        $this->request = $request;
    }

    public function getCrudRequest()
    {
        return $this->request;
    }
}
