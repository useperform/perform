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
            if (!isset($tag[0]['entity'])) {
                throw new \InvalidArgumentException(sprintf('The service %s tagged with "admin_base.admin" must set the "entity" option in the tag.', $service));
            }
            $entityAlias = $tag[0]['entity'];
            $entityClass = isset($tag[0]['entityClass']) ? $tag[0]['entityClass'] : $this->guessEntityClass($entityAlias);

            $definition->addMethodCall('addAdmin', [$entityAlias, $entityClass, $service]);
        }
    }

    public function guessEntityClass($entityAlias)
    {
        $pieces = explode(':', $entityAlias);
        if (!isset($pieces[1])) {
            throw new \InvalidArgumentException('Invalid entity alias.');
        }

        list($bundle, $entity) = $pieces;
        $namespace = trim(preg_replace('/([A-Z][a-z])+/', '\\\\\1', substr($bundle, 0, -6)).'Bundle', '\\');

        return $namespace.'\\Entity\\'.$entity;
    }
}
