<?php

namespace Perform\BaseBundle\Tests\Config;

use Perform\BaseBundle\Action\ActionRegistry;
use Perform\BaseBundle\Config\ActionConfig;
use Perform\BaseBundle\Action\ConfiguredAction;
use Perform\BaseBundle\Action\ActionInterface;
use Perform\BaseBundle\Admin\AdminRequest;

/**
 * ActionConfigTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ActionConfigTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->registry = $this->getMockBuilder(ActionRegistry::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->config = new ActionConfig($this->registry);
    }

    protected function stubRequest()
    {
        return $this->getMockBuilder(AdminRequest::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function stubAction(array $defaultConfig = [])
    {
        $action = $this->getMock(ActionInterface::class);
        $action->expects($this->any())
            ->method('getDefaultConfig')
            ->will($this->returnValue([]));

        return $action;
    }

    public function testAddInstance()
    {
        $action = $this->stubAction();
        $this->assertSame($this->config, $this->config->addInstance('some_action', $action));

        $ca = $this->config->get('some_action');
        $this->assertInstanceOf(ConfiguredAction::class, $ca);
        $this->assertFalse($ca->isLink());

        $options = ['opt' => true];
        $entities = [new \stdClass];
        $action->expects($this->once())
            ->method('run')
            ->with($entities, $options);
        $ca->run($entities, $options);
    }

    public function testAddNoLabels()
    {
        $action = $this->stubAction();
        $this->registry->expects($this->any())
            ->method('getAction')
            ->with('foo')
            ->will($this->returnValue($action));

        $this->config->add('foo');

        $ca = $this->config->all()['foo'];
        $this->assertInstanceOf(ConfiguredAction::class, $ca);
        $this->assertSame('foo', $ca->getName());
        $this->assertSame('Foo', $ca->getLabel($this->stubRequest(), new \stdClass()));
        $this->assertSame('Foo', $ca->getBatchLabel($this->stubRequest()));
        $this->assertFalse($ca->isLink());
    }

    public function testAddWithStringLabels()
    {
        $action = $this->stubAction();
        $this->registry->expects($this->any())
            ->method('getAction')
            ->with('foo')
            ->will($this->returnValue($action));
        $this->config->add('foo', [
            'label' => 'Foo label',
            'batchLabel' => 'Foo batch label',
        ]);

        $ca = $this->config->all()['foo'];
        $this->assertInstanceOf(ConfiguredAction::class, $ca);
        $this->assertSame('foo', $ca->getName());
        $this->assertSame('Foo label', $ca->getLabel($this->stubRequest(), new \stdClass()));
        $this->assertSame('Foo batch label', $ca->getBatchLabel($this->stubRequest()));
    }

    public function testAddLink()
    {
        $this->assertSame($this->config, $this->config->addLink('http://example.com', 'Test link'));

        $ca = $this->config->get('link_0');
        $this->assertInstanceOf(ConfiguredAction::class, $ca);
        $this->assertSame('link_0', $ca->getName());
        $this->assertSame('Test link', $ca->getLabel($this->stubRequest(), new \stdClass()));
        $this->assertTrue($ca->isLink());
        $this->assertSame('http://example.com', $ca->getLink(new \stdClass()));
    }

    public function testLinkIndexIsIncremented()
    {
        $this->config->addLink('http://example.com', 'Link 0');
        $this->config->addLink('http://example.com', 'Link 1');
        $this->config->addLink('http://example.com', 'Link 2');
        $this->config->addLink('http://example.com', 'Link 3');

        $this->assertSame(['link_0', 'link_1', 'link_2', 'link_3'], array_keys($this->config->all()));
    }

    public function testGetForEntity()
    {
        $entity = new \stdClass();
        $one = $this->getMock(ActionInterface::class);
        $one->expects($this->any())
            ->method('getDefaultConfig')
            ->will($this->returnValue([
                'isGranted' => true,
            ]));
        $two = $this->getMock(ActionInterface::class);
        $two->expects($this->any())
            ->method('getDefaultConfig')
            ->will($this->returnValue([
                'isGranted' => false,
            ]));
        $this->config->addInstance('foo', $one)->addInstance('bar', $two);

        $allowed = $this->config->getForEntity($entity);
        $this->assertSame(1, count($allowed));
        $this->assertInstanceOf(ConfiguredAction::class, $allowed[0]);
        $this->assertSame('foo', $allowed[0]->getName());
    }

    public function testGetButtonsForEntity()
    {
        $entity = new \stdClass();
        $one = $this->getMock(ActionInterface::class);
        $one->expects($this->any())
            ->method('getDefaultConfig')
            ->will($this->returnValue([
                'isButtonAvailable' => false,
            ]));
        $two = $this->getMock(ActionInterface::class);
        $two->expects($this->any())
            ->method('getDefaultConfig')
            ->will($this->returnValue([
                'isButtonAvailable' => true,
            ]));
        $this->config->addInstance('foo', $one)->addInstance('bar', $two);

        $allowed = $this->config->getButtonsForEntity($this->stubRequest(), $entity);
        $this->assertSame(1, count($allowed));
        $this->assertInstanceOf(ConfiguredAction::class, $allowed[0]);
        $this->assertSame('bar', $allowed[0]->getName());
    }

    public function testGetBatchOptionsForRequest()
    {
        $entity = new \stdClass();
        $one = $this->getMock(ActionInterface::class);
        $one->expects($this->any())
            ->method('getDefaultConfig')
            ->will($this->returnValue([
                'isBatchOptionAvailable' => function($request) {
                    return $request instanceof AdminRequest; //true
                },
            ]));
        $two = $this->getMock(ActionInterface::class);
        $two->expects($this->any())
            ->method('getDefaultConfig')
            ->will($this->returnValue([
                'isBatchOptionAvailable' => false,
            ]));
        $this->config->addInstance('foo', $one)->addInstance('bar', $two);

        $allowed = $this->config->getBatchOptionsForRequest($this->stubRequest());
        $this->assertSame(1, count($allowed));
        $this->assertInstanceOf(ConfiguredAction::class, $allowed[0]);
        $this->assertSame('foo', $allowed[0]->getName());
    }
}
