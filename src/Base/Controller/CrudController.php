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
            '/list' => 'list',
            '/view' => 'view',
            '/create' => 'create',
            '/edit' => 'edit',
            '/delete' => 'delete',
        ];
    }

    /**
     * @Template
     */
    public function listAction()
    {
        return [];
    }

    /**
     * @Template
     */
    public function viewAction()
    {
        return [];
    }

    /**
     * @Template
     */
    public function createAction()
    {
        return [];
    }

    /**
     * @Template
     */
    public function editAction()
    {
        return [];
    }

    /**
     * @Template
     */
    public function deleteAction()
    {
        return [];
    }
}
