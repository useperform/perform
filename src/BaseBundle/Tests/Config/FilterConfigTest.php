<?php

namespace Perform\BaseBundle\Tests\Config;

use Perform\BaseBundle\Filter\FilterConfig;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

/**
 * FilterConfigTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FilterConfigTest extends \PHPUnit_Framework_TestCase
{
    protected $config;

    public function setUp()
    {
        $this->config = new FilterConfig();
    }

    public function testGetNoFilters()
    {
        $this->assertSame([], $this->config->getFilters());
    }

    public function testAddEmptyFilter()
    {
        $this->assertSame($this->config, $this->config->add('test', [
            'query' => function($qb) {
                return $qb;
            },
        ]));
        $filters = $this->config->getFilters();
        $this->assertArrayHasKey('query', $filters['test']->getConfig());
    }

    public function testQueryMustBeSupplied()
    {
        $this->setExpectedException(MissingOptionsException::class);
        $this->config->add('enabled', []);
    }

    public function testGetFilter()
    {
        $this->assertSame($this->config, $this->config->add('test', [
            'query' => function($qb) {
                return $qb;
            },
        ]));
        $filter = $this->config->getFilter('test')->getConfig();
        $this->assertArrayHasKey('query', $filter);
    }

    public function testSensibleLabelIsGiven()
    {
        $this->config->add('superTitle', ['query' => function() {}]);
        $this->assertSame('Super title', $this->config->getFilter('superTitle')->getConfig()['label']);
    }

    public function testLabelCanBeOverridden()
    {
        $this->config->add('superTitle', [
            'query' => function() {},
            'label' => 'Title',
        ]);
        $this->assertSame('Title', $this->config->getFilter('superTitle')->getConfig()['label']);
    }

    // public function testCountingCanBeEnabled()
    // {
    //     $this->config->add('enabled', [
    //         'query' => function($qb) {
    //             return $qb->where('e.enabled = true');
    //         },
    //         'count' => false,
    //     ]);
    // }
}
