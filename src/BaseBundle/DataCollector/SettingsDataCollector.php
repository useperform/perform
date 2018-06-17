<?php

namespace Perform\BaseBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Perform\BaseBundle\Settings\Manager\TraceableManager;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SettingsDataCollector extends DataCollector
{
    protected $settings;

    public function __construct(TraceableManager $settings)
    {
        $this->settings = $settings;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data['getCalls'] = $this->settings->getGetCalls();
        $this->data['setCalls'] = $this->settings->getSetCalls();
        $this->data['used'] = isset($this->data['getCalls'][0]) || isset($this->data['setCalls'][0]);
        $this->data['managerClass'] = get_class($this->settings->getInnerManager());
    }

    public function reset()
    {
        $this->data = [];
    }

    public function getName()
    {
        return 'perform_base.settings';
    }

    public function getGetCalls()
    {
        return array_map(function ($data) {
            return [
                $data[0],
                $this->cloneVar($data[1]),
                $data[2],
            ];
        }, $this->data['getCalls']);
    }

    public function getDefaultGetCalls()
    {
        return array_filter($this->getGetCalls(), function ($data) {
            return $data[2] === false;
        });
    }

    public function getSetCalls()
    {
        return array_map(function ($data) {
            return [
                $data[0],
                $this->cloneVar($data[1]),
            ];
        }, $this->data['setCalls']);
    }

    public function getUsed()
    {
        return $this->data['used'];
    }

    public function getManagerClass()
    {
        return $this->data['managerClass'];
    }
}
