<?php

namespace Admin\Base\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * CrudController
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
abstract class CrudController extends Controller
{
    public static function getCrudActions()
    {
        return [
            '/' => 'list',
            '/view' => 'view',
            '/create' => 'create',
            '/edit' => 'edit',
            '/delete' => 'delete',
        ];
    }

    public function listAction()
    {
        return [];
    }

    public function viewAction()
    {
        return [];
    }

    public function createAction()
    {
        return [];
    }

    public function editAction()
    {
        return [];
    }

    public function deleteAction()
    {
        return [];
    }
}
