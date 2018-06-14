<?php

namespace Perform\BaseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;

/**
 * Only allow the CrudVoter to be enabled with the correct security
 * decision strategy.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudVoterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $dmService = 'security.access.decision_manager';
        $voterService = 'perform_base.voter.crud';
        if (!$container->hasDefinition($dmService) || !$container->hasDefinition($voterService)) {
            return;
        }
        $strategy = $container->getDefinition($dmService)->getArgument(1);

        if ($strategy !== AccessDecisionManager::STRATEGY_UNANIMOUS) {
            throw new \InvalidArgumentException(sprintf(
                'To enable the CrudVoter in the PerformBaseBundle, you must set the security access decision strategy to "unanimous". The current strategy ("%s") may result in unintentionally granted permissions with the voter enabled. Either set the "security.access_decision_manager.strategy" configuration node to "unanimous", or disable the voter by setting the "perform_base.security.crud_voter" node to "false".',
                $strategy
            ));
        }
    }
}
