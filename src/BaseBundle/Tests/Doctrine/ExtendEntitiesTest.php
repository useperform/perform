<?php

namespace Perform\BaseBundle\Tests\Doctrine;

use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\ExtendEntitiesKernel;
use Temping\Temping;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\YamlBaseBundle\YamlBaseBundle;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\XmlExtendBundle\XmlExtendBundle;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\XmlExtendBundle\Entity\YamlItem;

/**
 * ExtendEntitiesTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ExtendEntitiesTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->temp = new Temping();
    }

    private function useBundles(array $bundles)
    {
        $this->kernel = new ExtendEntitiesKernel($this->temp->getDirectory(), $bundles);
        $this->kernel->boot();
    }

    public function tearDown()
    {
        $this->temp->reset();
    }

    public function testExtendWithXml()
    {
        $this->useBundles([
            new YamlBaseBundle(),
            new XmlExtendBundle(),
        ]);
        $em = $this->kernel->getContainer()->get('doctrine.orm.entity_manager');

        $yamlBase = $em->getClassMetadata('YamlBaseBundle:Item');
        $this->assertTrue($yamlBase->isMappedSuperclass);
        $yamlChild = $em->getClassMetadata('XmlExtendBundle:YamlItem');
        $this->assertSame(['id', 'name', 'extraField'], array_keys($yamlChild->fieldMappings));
        $related = $em->getClassMetadata('YamlBaseBundle:ItemLink');
        $this->assertSame(YamlItem::class, $related->associationMappings['item']['targetEntity']);
    }
}
