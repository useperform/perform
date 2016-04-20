<?php

namespace Admin\NotificationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Admin\NotificationBundle\DependencyInjection\Compiler\RegisterPublishersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AdminNotificationBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new RegisterPublishersPass());
    }
}
