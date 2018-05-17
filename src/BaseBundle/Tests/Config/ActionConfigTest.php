<?php

namespace Perform\BaseBundle\Tests\Config;

use Perform\BaseBundle\Action\ActionRegistry;
use Perform\BaseBundle\Config\ActionConfig;
use Perform\BaseBundle\Action\ConfiguredAction;
use Perform\BaseBundle\Action\ActionInterface;
use Perform\BaseBundle\Crud\CrudRequest;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Perform\BaseBundle\Routing\CrudUrlGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
        $this->authChecker = $this->getMock(AuthorizationCheckerInterface::class);
        $this->config = new ActionConfig($this->registry, $this->authChecker);
    }

    protected function stubRequest()
    {
        return $this->getMockBuilder(CrudRequest::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function stubAction(array $defaultConfig = [])
    {
        $action = $this->getMock(ActionInterface::class);
        $action->expects($this->any())
            ->method('getDefaultConfig')
            ->will($this->returnValue($defaultConfig));

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
        $entities = [new \stdClass()];
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
        $link = $ca->getLink(new \stdClass(), $this->getMock(CrudUrlGeneratorInterface::class), $this->getMock(UrlGeneratorInterface::class));
        $this->assertSame('http://example.com', $link);
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
        $one = $this->stubAction([
            'isGranted' => true,
        ]);
        $two = $this->stubAction([
            'isGranted' => false,
        ]);
        $this->config->addInstance('foo', $one)->addInstance('bar', $two);

        $allowed = $this->config->getForEntity(new \stdClass());
        $this->assertSame(1, count($allowed));
        $this->assertInstanceOf(ConfiguredAction::class, $allowed[0]);
        $this->assertSame('foo', $allowed[0]->getName());
    }

    public function testGetButtonsForEntity()
    {
        $one = $this->stubAction([
            'isButtonAvailable' => false,
        ]);
        $two = $this->stubAction([
            'isButtonAvailable' => true,
        ]);
        $this->config->addInstance('foo', $one)->addInstance('bar', $two);

        $allowed = $this->config->getButtonsForEntity(new \stdClass(), $this->stubRequest());
        $this->assertSame(1, count($allowed));
        $this->assertInstanceOf(ConfiguredAction::class, $allowed[0]);
        $this->assertSame('bar', $allowed[0]->getName());
    }

    public function testGetButtonsForEntityAlsoChecksIsGranted()
    {
        $one = $this->stubAction([
            'isButtonAvailable' => true,
            'isGranted' => true,
        ]);
        $two = $this->stubAction([
            'isButtonAvailable' => true,
            'isGranted' => false,
        ]);
        $this->config->addInstance('foo', $one)->addInstance('bar', $two);

        $allowed = $this->config->getButtonsForEntity(new \stdClass(), $this->stubRequest());
        $this->assertSame(1, count($allowed));
        $this->assertInstanceOf(ConfiguredAction::class, $allowed[0]);
        $this->assertSame('foo', $allowed[0]->getName());
    }

    public function testGetBatchOptionsForRequest()
    {
        $entity = new \stdClass();
        $one = $this->stubAction([
            'isBatchOptionAvailable' => function ($request) {
                return $request instanceof CrudRequest; //true
            },
        ]);
        $two = $this->stubAction([
            'isBatchOptionAvailable' => false,
        ]);
        $this->config->addInstance('foo', $one)->addInstance('bar', $two);

        $allowed = $this->config->getBatchOptionsForRequest($this->stubRequest());
        $this->assertSame(1, count($allowed));
        $this->assertInstanceOf(ConfiguredAction::class, $allowed[0]);
        $this->assertSame('foo', $allowed[0]->getName());
    }
}
