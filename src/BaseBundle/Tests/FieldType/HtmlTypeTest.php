<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\FieldType\HtmlType;
use Perform\BaseBundle\Asset\AssetContainer;
use Symfony\Component\Form\FormBuilderInterface;
use Perform\BaseBundle\Test\FieldTypeTestCase;
use Perform\BaseBundle\Test\WhitespaceAssertions;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 * @group kernel
 **/
class HtmlTypeTest extends FieldTypeTestCase
{
    use WhitespaceAssertions;

    protected $assets;

    public function registerTypes()
    {
        $this->assets = new AssetContainer();

        return [
            'html' => new HtmlType($this->assets),
        ];
    }

    public function testViewContext()
    {
        $entity = new \stdClass();
        $entity->markup = '<div><h2>HTML</h2></div>';

        $this->config->add('markup', [
            'type' => 'html',
        ]);
        $this->assertTrimmedString('<div><h2>HTML</h2></div>', $this->viewContext($entity, 'markup'));
    }
}
