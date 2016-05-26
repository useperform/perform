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
        $adminConfiguration = $container->getParameter('admin_base.admins');
        $entityAliases = $container->getParameter('admin_base.entity_aliases');

        foreach ($container->findTaggedServiceIds('admin_base.admin') as $service => $tag) {
            if (!isset($tag[0]['entity'])) {
                throw new \InvalidArgumentException(sprintf('The service "%s" tagged with "admin_base.admin" must set the "entity" option in the tag.', $service));
            }
            $entityAlias = $tag[0]['entity'];
            if (!isset($entityAliases[$entityAlias])) {
                throw new \InvalidArgumentException(sprintf('The service "%s" references an unknown entity "%s".', $service, $entityAlias));
            }

            $entityClass = $entityAliases[$entityAlias];

            $definition->addMethodCall('addAdmin', [$entityAlias, $entityClass, $service]);
            if (isset($adminConfiguration[$entityAlias])) {
                $adminDefinition = $container->getDefinition($service);
                $adminDefinition->addMethodCall('configure', [$adminConfiguration[$entityAlias]]);
            }
        }
    }
}
