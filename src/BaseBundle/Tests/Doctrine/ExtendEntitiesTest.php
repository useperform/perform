<?php

namespace Perform\BaseBundle\Tests\Doctrine;

use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\ExtendEntitiesKernel;
use Temping\Temping;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\YamlParentBundle\YamlParentBundle;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\XmlChildBundle\XmlChildBundle;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\XmlChildBundle\Entity\YamlItem;

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
            new XmlChildBundle(),
        ]);
        $em = $this->kernel->getContainer()->get('doctrine.orm.entity_manager');

        $yamlParent = $em->getClassMetadata('YamlParentBundle:Item');
        $this->assertTrue($yamlParent->isMappedSuperclass);
        $yamlChild = $em->getClassMetadata('XmlChildBundle:YamlItem');
        $this->assertSame(['id', 'name', 'extraField'], array_keys($yamlChild->fieldMappings));
        $this->assertSame(['links'], array_keys($yamlChild->associationMappings));
        $related = $em->getClassMetadata('YamlParentBundle:ItemLink');
        $this->assertSame(YamlItem::class, $related->associationMappings['item']['targetEntity']);
    }
}
