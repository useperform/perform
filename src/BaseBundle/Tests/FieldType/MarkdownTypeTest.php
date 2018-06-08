<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\FieldType\MarkdownType;
use Perform\BaseBundle\Test\TypeTestCase;
use Perform\BaseBundle\Test\WhitespaceAssertions;
use League\CommonMark\CommonMarkConverter;
use Perform\BaseBundle\Asset\AssetContainer;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 * @group kernel
 **/
class MarkdownTypeTest extends TypeTestCase
{
    use WhitespaceAssertions;

    protected function registerTypes()
    {
        return [
            'md' => new MarkdownType(new CommonMarkConverter(), new AssetContainer()),
        ];
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
        $expected = '<div class="p-markdown"><h1>big title</h1><h2>little title</h2><p>text</p></div>';
        $this->assertTrimmedString($expected, $this->renderer->listContext($obj, 'content', $config));
    }
}
