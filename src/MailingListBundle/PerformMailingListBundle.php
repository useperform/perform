<?php

namespace Perform\MailingListBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Perform\NotificationBundle\DependencyInjection\Compiler\RegisterPublishersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\MailingListBundle\DependencyInjection\Compiler\ConfigureManagerPass;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformMailingListBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ConfigureManagerPass());
    }
}
