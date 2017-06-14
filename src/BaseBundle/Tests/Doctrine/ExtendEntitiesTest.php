<?php

namespace Perform\BaseBundle\Tests\Doctrine;

use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\ExtendEntitiesKernel;
use Temping\Temping;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\YamlParentBundle\YamlParentBundle;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\XmlParentBundle\XmlParentBundle;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\XmlChildBundle\XmlChildBundle;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\XmlChildBundle\Entity\YamlItem;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\XmlChildBundle\Entity\XmlItem;

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
            new YamlParentBundle(),
            new XmlParentBundle(),
            new XmlChildBundle(),
        ]);
        $em = $this->kernel->getContainer()->get('doctrine.orm.entity_manager');

        $yamlParent = $em->getClassMetadata('YamlParentBundle:Item');
        $this->assertTrue($yamlParent->isMappedSuperclass);
        $yamlChild = $em->getClassMetadata('XmlChildBundle:YamlItem');
        $this->assertSame(['id', 'name', 'extraField'], array_keys($yamlChild->fieldMappings));
        $this->assertSame(['links'], array_keys($yamlChild->associationMappings));
        $yamlRelated = $em->getClassMetadata('YamlParentBundle:ItemLink');
        $this->assertSame(YamlItem::class, $yamlRelated->associationMappings['item']['targetEntity']);

        $xmlParent = $em->getClassMetadata('XmlParentBundle:Item');
        $this->assertTrue($xmlParent->isMappedSuperclass);
        $xmlChild = $em->getClassMetadata('XmlChildBundle:XmlItem');
        $this->assertEquals(['id', 'name', 'extraField'], array_keys($xmlChild->fieldMappings), "", 0, 10, true);
        $this->assertSame(['links'], array_keys($xmlChild->associationMappings));
        $xmlRelated = $em->getClassMetadata('XmlParentBundle:ItemLink');
        $this->assertSame(XmlItem::class, $xmlRelated->associationMappings['item']['targetEntity']);
    }
}
