<?php

namespace Admin\Team\Controller;

use Admin\Base\Controller\CrudController;

/**
 * TeamController
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TeamController extends CrudController
{
    protected $entity = 'AdminTeamBundle:TeamMember';
}
