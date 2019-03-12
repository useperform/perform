<?php

namespace Perform\BaseBundle\Tests\Crud;

use PHPUnit\Framework\TestCase;
use Perform\BaseBundle\FieldType\FieldTypeRegistry;
use Perform\BaseBundle\Crud\ContextRenderer;
use Perform\BaseBundle\FieldType\FieldTypeInterface;
use Twig\Template;
use Symfony\Component\Form\FormView;
use Perform\BaseBundle\Crud\CrudRequest;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ContextRendererTest extends TestCase
{
    protected $registry;
    protected $twig;
    protected $renderer;

    public function setUp()
    {
        $this->registry = $this->getMockBuilder(FieldTypeRegistry::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->twig = $this->getMockBuilder(\Twig_Environment::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $this->renderer = new ContextRenderer($this->registry, $this->twig);
    }

    protected function mockType($name, $templateName)
    {
        $type = $this->createMock(FieldTypeInterface::class);
        $this->registry->expects($this->any())
            ->method('getType')
            ->with($name)
            ->will($this->returnValue($type));
        $template = $this->getMockBuilder(Template::class)
                  ->disableOriginalConstructor()
                  ->getMock();
        $this->twig->expects($this->any())
            ->method('loadTemplate')
            ->with($templateName)
            ->will($this->returnValue($template));

        return [$type, $template];
    }

    public function testListContext()
    {
        $entity = new \stdClass();
        $listOptions = ['some_option' => true];
        $config = [
            'type' => 'some_type',
            'template' => 'some_template.html.twig',
            'listOptions' => $listOptions,
        ];
        list($type, $template) = $this->mockType('some_type', 'some_template.html.twig');
        $type->expects($this->any())
            ->method('listContext')
            ->with($entity, 'title', $listOptions)
            ->will($this->returnValue([]));
        $template->expects($this->any())
            ->method('renderBlock')
            ->with('list', ['entity' => $entity, 'field' => 'title'])
            ->will($this->returnValue('<span>Entity title</span>'));

        $this->assertSame('<span>Entity title</span>', $this->renderer->listContext($entity, 'title', $config));
    }

    public function testViewContext()
    {
        $entity = new \stdClass();
        $viewOptions = ['some_option' => false];
        $config = [
            'type' => 'some_type',
            'template' => 'template.html.twig',
            'viewOptions' => $viewOptions,
        ];
        list($type, $template) = $this->mockType('some_type', 'template.html.twig');
        $type->expects($this->any())
            ->method('viewContext')
            ->with($entity, 'title', $viewOptions)
            ->will($this->returnValue([]));
        $template->expects($this->any())
            ->method('renderBlock')
            ->with('view', ['entity' => $entity, 'field' => 'title'])
            ->will($this->returnValue('<span>Entity title</span>'));

        $this->assertSame('<span>Entity title</span>', $this->renderer->viewContext($entity, 'title', $config));
    }

    public function testCreateContext()
    {
        $entity = new \stdClass();
        $createOptions = ['some_option' => true, 'label' => 'Title'];
        $config = [
            'type' => 'another_type',
            'template' => 'template.html.twig',
            'createOptions' => $createOptions,
        ];

        $formView = new FormView();
        $formView->vars['type_vars']['title'] = ['format' => 1];
        list($type, $template) = $this->mockType('another_type', 'template.html.twig');
        $type->expects($this->any())
            ->method('createContext')
            ->with($entity, 'title', $createOptions, $formView)
            ->will($this->returnValue('Entity title'));
        $vars = [
            'field' => 'title',
            'form' => $formView,
            'label' => 'Title',
            'entity' => $entity,
            'context' => CrudRequest::CONTEXT_CREATE,
            'type_vars' => ['format' => 1],
        ];
        $template->expects($this->any())
            ->method('renderBlock')
            ->with('create', $vars)
            ->will($this->returnValue('<form></form>'));

        $this->assertSame('<form></form>', $this->renderer->createContext($entity, 'title', $config, $formView));
    }

    public function testEditContext()
    {
        $entity = new \stdClass();
        $editOptions = ['some_option' => true, 'label' => 'Title'];
        $config = [
            'type' => 'another_type',
            'template' => 'template.html.twig',
            'editOptions' => $editOptions,
        ];

        $formView = new FormView();
        $formView->vars['type_vars']['title'] = ['format' => 1];
        list($type, $template) = $this->mockType('another_type', 'template.html.twig');
        $type->expects($this->any())
            ->method('editContext')
            ->with($entity, 'title', $editOptions, $formView)
            ->will($this->returnValue('Entity title'));
        $vars = [
            'field' => 'title',
            'form' => $formView,
            'label' => 'Title',
            'entity' => $entity,
            'context' => CrudRequest::CONTEXT_EDIT,
            'type_vars' => ['format' => 1],
        ];
        $template->expects($this->any())
            ->method('renderBlock')
            ->with('edit', $vars)
            ->will($this->returnValue('<form></form>'));

        $this->assertSame('<form></form>', $this->renderer->editContext($entity, 'title', $config, $formView));
    }
}
