<?php

namespace Perform\BaseBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Perform\BaseBundle\Crud\CrudRegistry;
use Perform\BaseBundle\EventListener\CrudTemplateListener;
use Perform\BaseBundle\Test\Services;
use Twig\Environment;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Perform\BaseBundle\Controller\CrudController;
use Perform\BaseBundle\Crud\CrudInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudTemplateListenerTest extends TestCase
{
    protected $registry;
    protected $twig;
    protected $listener;

    public function setUp()
    {
        $this->registry = $this->getMockBuilder(CrudRegistry::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->twig = $this->getMockBuilder(Environment::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $this->listener = new CrudTemplateListener(Services::ServiceLocator([
            'registry' => $this->registry,
            'twig' => $this->twig,
        ]));
    }

    public function testCrudActionSetsTemplate()
    {
        $crudController = $this->getMockBuilder(CrudController::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $controller = [$crudController, 'viewAction'];
        $request = new Request();
        $request->attributes->set('_crud', 'some_crud');
        $event = new FilterControllerEvent(
            $this->createMock(HttpKernelInterface::class),
            $controller,
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );
        $crud = $this->createMock(CrudInterface::class);
        $crud->expects($this->any())
            ->method('getTemplate')
            ->with($this->twig, 'some_crud', 'view')
            ->will($this->returnValue('@PerformBase/crud/view.html.twig'));

        $this->registry->expects($this->any())
            ->method('get')
            ->with('some_crud')
            ->will($this->returnValue($crud));

        $this->listener->onKernelController($event);

        $annotation = $event->getRequest()->attributes->get('_template');
        $this->assertInstanceOf(Template::class, $annotation);
        $this->assertSame('@PerformBase/crud/view.html.twig', $annotation->getTemplate());
        $this->assertSame($controller, $annotation->getOwner());
    }
}
