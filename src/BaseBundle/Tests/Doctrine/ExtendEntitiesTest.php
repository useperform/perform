<?php

namespace Perform\BaseBundle\Tests\Doctrine;

use Perform\BaseBundle\Test\TestKernel;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\YamlParentBundle\YamlParentBundle;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\XmlParentBundle\XmlParentBundle;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\XmlChildBundle\XmlChildBundle;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\XmlChildBundle\Entity\YamlItem as XmlExtendYamlItem;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\XmlChildBundle\Entity\XmlItem as XmlExtendXmlItem;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\YamlChildBundle\YamlChildBundle;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\YamlChildBundle\Entity\YamlItem as YamlExtendYamlItem;
use Perform\BaseBundle\Tests\Fixtures\ExtendEntities\YamlChildBundle\Entity\XmlItem as YamlExtendXmlItem;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 * @group kernel
 **/
class ExtendEntitiesTest extends \PHPUnit_Framework_TestCase
{
    protected $kernel;

    private function configure(array $bundles, $extraConfig)
    {
        $this->kernel = new TestKernel($bundles, __DIR__.'/../Fixtures/ExtendEntities/'.$extraConfig);
        $this->kernel->boot();
    }

    public function tearDown()
    {
        $this->kernel->shutdown();
    }

    public function testExtendWithXml()
    {
        $this->configure([
            new YamlParentBundle(),
            new XmlParentBundle(),
            new XmlChildBundle(),
        ], 'xml_child.yml');
        $em = $this->kernel->getContainer()->get('doctrine.orm.entity_manager');

        $yamlParent = $em->getClassMetadata('YamlParentBundle:Item');
        $this->assertTrue($yamlParent->isMappedSuperclass);
        $yamlChild = $em->getClassMetadata('XmlChildBundle:YamlItem');
        $this->assertSame(['id', 'name', 'extraField'], array_keys($yamlChild->fieldMappings));
        $this->assertSame(['links'], array_keys($yamlChild->associationMappings));
        $yamlRelated = $em->getClassMetadata('YamlParentBundle:ItemLink');
        $this->assertSame(XmlExtendYamlItem::class, $yamlRelated->associationMappings['item']['targetEntity']);

        $xmlParent = $em->getClassMetadata('XmlParentBundle:Item');
        $this->assertTrue($xmlParent->isMappedSuperclass);
        $xmlChild = $em->getClassMetadata('XmlChildBundle:XmlItem');
        $this->assertEquals(['id', 'name', 'extraField'], array_keys($xmlChild->fieldMappings), '', 0, 10, true);
        $this->assertSame(['links'], array_keys($xmlChild->associationMappings));
        $xmlRelated = $em->getClassMetadata('XmlParentBundle:ItemLink');
        $this->assertSame(XmlExtendXmlItem::class, $xmlRelated->associationMappings['item']['targetEntity']);
    }

    public function testExtendWithYaml()
    {
        $this->configure([
            new YamlParentBundle(),
            new XmlParentBundle(),
            new YamlChildBundle(),
        ], 'yaml_child.yml');
        $em = $this->kernel->getContainer()->get('doctrine.orm.entity_manager');

        $yamlParent = $em->getClassMetadata('YamlParentBundle:Item');
        $this->assertTrue($yamlParent->isMappedSuperclass);
        $yamlChild = $em->getClassMetadata('YamlChildBundle:YamlItem');
        $this->assertSame(['id', 'name', 'extraField'], array_keys($yamlChild->fieldMappings));
        $this->assertSame(['links'], array_keys($yamlChild->associationMappings));
        $yamlRelated = $em->getClassMetadata('YamlParentBundle:ItemLink');
        $this->assertSame(YamlExtendYamlItem::class, $yamlRelated->associationMappings['item']['targetEntity']);

        $xmlParent = $em->getClassMetadata('XmlParentBundle:Item');
        $this->assertTrue($xmlParent->isMappedSuperclass);
        $yamlChild = $em->getClassMetadata('YamlChildBundle:XmlItem');
        $this->assertEquals(['id', 'name', 'extraField'], array_keys($yamlChild->fieldMappings), '', 0, 10, true);
        $this->assertSame(['links'], array_keys($yamlChild->associationMappings));
        $xmlRelated = $em->getClassMetadata('XmlParentBundle:ItemLink');
        $this->assertSame(YamlExtendXmlItem::class, $xmlRelated->associationMappings['item']['targetEntity']);
    }
}
