<?php

namespace Perform\MailingListBundle\Crud;

use Perform\BaseBundle\Crud\AbstractCrud;
use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Config\FilterConfig;
use Perform\BaseBundle\Config\ActionConfig;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LocalListCrud extends AbstractCrud
{
    protected $routePrefix = 'perform_mailing_list_local_list_';

    public function configureTypes(TypeConfig $config)
    {
        $config->add('name', [
                'type' => 'string',
            ])
            ->add('slug', [
                'type' => 'string',
            ]);
    }

    public function configureFilters(FilterConfig $config)
    {
    }

    public function configureActions(ActionConfig $config)
    {
        parent::configureActions($config);
    }
}
