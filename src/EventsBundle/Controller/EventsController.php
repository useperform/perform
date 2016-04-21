<?php

namespace Admin\EventsBundle\Controller;

use Admin\Base\Controller\CrudController;

/**
 * EventsController
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EventsController extends CrudController
{
    protected $entity = 'AdminEventsBundle:Event';
}
