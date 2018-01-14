<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\Type\MarkdownType;
use Perform\BaseBundle\Test\TypeTestCase;
use Perform\BaseBundle\Test\WhitespaceAssertions;
use League\CommonMark\CommonMarkConverter;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MarkdownTypeTest extends TypeTestCase
{
    use WhitespaceAssertions;

    protected function configure()
    {
        $type = new MarkdownType(new CommonMarkConverter());
        $this->mockService('md_service', $type);
        $this->typeRegistry->addTypeService('md', 'md_service');
    }

    public function testListContext()
    {
        $obj = new \stdClass();
        $obj->content = <<<EOT
# big title
## little title
text
EOT;
        $config = [
            'type' => 'md',
            'listOptions' => [],
            'template' => '@PerformBase/type/markdown.html.twig',
        ];
        $this->assertTrimmedString('<h1>big title</h1><h2>little title</h2><p>text</p>', $this->renderer->listContext($obj, 'content', $config));
    }
}
