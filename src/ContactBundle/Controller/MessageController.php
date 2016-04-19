<?php

namespace Admin\ContactBundle\Controller;

use Admin\Base\Controller\CrudController;

class MessageController extends CrudController
{
    protected $entity = 'AdminContactBundle:Message';

    public static function getCrudActions()
    {
        return [
            '/' => 'list',
            '/view/{id}' => 'view',
        ];
    }
}
