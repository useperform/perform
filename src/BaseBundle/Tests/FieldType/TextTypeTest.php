<?php

namespace Perform\BaseBundle\Tests\FieldType;

use Perform\BaseBundle\FieldType\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Perform\BaseBundle\Test\FieldTypeTestCase;
use Perform\BaseBundle\Test\WhitespaceAssertions;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TextTypeTest extends FieldTypeTestCase
{
    use WhitespaceAssertions;

    protected function registerTypes()
    {
        return [
            'text' => new TextType(),
        ];
    }

    public function testCreateContext()
    {
        $builder = $this->getMock(FormBuilderInterface::class);
        $builder->expects($this->once())
            ->method('add')
            ->with('forename', TextareaType::class);

        $this->getType('text')->createContext($builder, 'forename');
    }

    public function testListContext()
    {
        $article = new \stdClass();
        $article->content = <<<EOF
Article One

This is an article about something interesting
EOF;

        $this->config->add('content', [
            'type' => 'text',
        ]);
        $this->assertTrimmedString('Article One  This is an article about something in…', $this->listContext($article, 'content'));
    }

    public function testViewContext()
    {
        $article = new \stdClass();
        $article->content = <<<EOF
Article One

This is an article about something interesting
EOF;

        $this->config->add('content', [
            'type' => 'text',
        ]);
        $this->assertTrimmedString('Article One<br /><br />This is an article about something interesting', $this->viewContext($article, 'content'));
    }
}
