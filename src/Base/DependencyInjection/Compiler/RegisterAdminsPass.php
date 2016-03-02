<?php

namespace Admin\Base\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Register admins automatically.
 **/
class RegisterAdminsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('admin_base.admin.registry');

        foreach ($container->findTaggedServiceIds('admin_base.admin') as $service => $tag) {
            if (!isset($tag[0]['entityClass'])) {
                throw new \InvalidArgumentException(sprintf('The service %s tagged with "admin_base.admin" must set "entityClass" in the tag.', $service));
            }

            $definition->addMethodCall('addAdmin', [$tag[0]['entityClass'], $service]);
        }
    }
}
