<?php

namespace Perform\BaseBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Perform\BaseBundle\Crud\CrudRegistry;
use Perform\BaseBundle\Config\ConfigStoreInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\TraceableAccessDecisionManager;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudDataCollector extends DataCollector
{
    protected $registry;
    protected $store;
    protected $accessDecisionManager;
    protected $extendedEntities;

    public function __construct(CrudRegistry $registry, ConfigStoreInterface $store, AccessDecisionManagerInterface $accessDecisionManager, array $extendedEntities)
    {
        $this->registry = $registry;
        $this->store = $store;
        $this->accessDecisionManager = $accessDecisionManager;
        $this->extendedEntities = $extendedEntities;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $crudServices = $this->registry->all();

        ksort($crudServices);
        $this->data = [
            'crud' => $crudServices,
            'extendedEntities' => $this->extendedEntities,
            'correctVoterStrategy' => $this->accessDecisionManager instanceof TraceableAccessDecisionManager ? $this->accessDecisionManager->getStrategy() === 'unanimous' : true,
        ];
        if ($request->attributes->has('_entity')) {
            $this->data['activeEntity'] = $request->attributes->get('_entity');
            $this->data['activeCrud'] = get_class($this->registry->getCrud($this->data['activeEntity']));
            $this->data['typeConfig'] = $this->store->getTypeConfig($this->data['activeEntity'])->getAllTypes();
            $this->sanitize($this->data['typeConfig']);
            $this->data['addedConfigs'] = $this->store->getTypeConfig($this->data['activeEntity'])->getAddedConfigs();
            $this->sanitize($this->data['addedConfigs']);
        }
    }

    protected function sanitize(&$array)
    {
        array_walk_recursive($array, function (&$value) {
            if ($value instanceof \Closure) {
                $value = '(function)';
            }
        });
    }

    public function getCrud()
    {
        return $this->data['crud'];
    }

    public function getActiveEntity()
    {
        return isset($this->data['activeEntity']) ? $this->data['activeEntity'] : null;
    }

    public function getActiveCrud()
    {
        return isset($this->data['activeCrud']) ? $this->data['activeCrud'] : null;
    }

    public function getActiveAlias()
    {
        $crud = $this->getActiveCrud();
        $pieces = explode('\\', $this->getActiveCrud());

        return array_pop($pieces);
    }

    public function getTypeConfig()
    {
        return isset($this->data['typeConfig']) ? $this->data['typeConfig'] : [];
    }

    public function getAddedConfigs()
    {
        return isset($this->data['addedConfigs']) ? $this->data['addedConfigs'] : [];
    }

    public function getExtendedEntities()
    {
        return $this->data['extendedEntities'];
    }

    public function getCorrectVoterStrategy()
    {
        return $this->data['correctVoterStrategy'];
    }

    public function getName()
    {
        return 'perform_base.crud';
    }
}
