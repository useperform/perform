<?php

namespace Perform\BaseBundle\Tests\FieldType;

use Perform\BaseBundle\FieldType\TagType;
use Perform\BaseBundle\Test\FieldTypeTestCase;
use Perform\BaseBundle\Test\WhitespaceAssertions;
use Perform\BaseBundle\Entity\Tag;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 * @group kernel
 **/
class TagTypeTest extends FieldTypeTestCase
{
    use WhitespaceAssertions;

    protected function registerTypes()
    {
        return [
            'tag' => new TagType(),
        ];
    }

    public function testListContext()
    {
        $obj = new \stdClass();
        $one = new Tag();
        $one->setTitle('Wonderful');
        $two = new Tag();
        $two->setTitle('Splendid');
        $obj->tags = new ArrayCollection([$one, $two]);
        $this->config->add('tags', [
            'type' => 'tag',
            'options' => [
                'discriminator' => 'testing',
            ],
        ]);
        $expected = '<span class="badge badge-secondary">Wonderful</span>';
        $expected .= '<span class="badge badge-secondary">Splendid</span>';
        $this->assertTrimmedString($expected, $this->listContext($obj, 'tags'));
    }
}
