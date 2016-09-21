<?php

namespace Perform\NotificationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Perform\NotificationBundle\DependencyInjection\Compiler\RegisterPublishersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PerformNotificationBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new RegisterPublishersPass());
    }
}
