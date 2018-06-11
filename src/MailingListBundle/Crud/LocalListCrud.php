<?php

namespace Perform\MailingListBundle\Crud;

use Perform\BaseBundle\Crud\AbstractCrud;
use Perform\BaseBundle\Config\FieldConfig;
use Perform\BaseBundle\Config\FilterConfig;
use Perform\BaseBundle\Config\ActionConfig;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LocalListCrud extends AbstractCrud
{
    public function configureFields(FieldConfig $config)
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
