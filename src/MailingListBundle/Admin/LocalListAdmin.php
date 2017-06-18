<?php

namespace Perform\MailingListBundle\Admin;

use Perform\BaseBundle\Admin\AbstractAdmin;
use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Config\FilterConfig;
use Perform\BaseBundle\Config\ActionConfig;

/**
 * LocalListAdmin.
 **/
class LocalListAdmin extends AbstractAdmin
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
