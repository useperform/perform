<?php

namespace Perform\RichContentBundle\Tests\Serializer;

use Perform\RichContentBundle\Repository\ContentRepository;
use Perform\RichContentBundle\Serializer\OperationDenormalizer;
use Perform\RichContentBundle\Persister\OperationInterface;
use Perform\RichContentBundle\Persister\CreateOperation;
use Perform\RichContentBundle\Persister\UpdateOperation;
use Perform\RichContentBundle\Entity\Content;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class OperationDenormalizerTest extends \PHPUnit_Framework_TestCase
{
    protected $denormalizer;

    public function setUp()
    {
        $this->repo = $this->getMockBuilder(ContentRepository::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $this->denormalizer = new OperationDenormalizer($this->repo);
    }

    public function testSupportsDenormalization()
    {
        $this->assertTrue($this->denormalizer->supportsDenormalization([], OperationInterface::class));
    }

    public function testDenormalizeCreate()
    {
        $data = [
            'blocks' => [],
            'newBlocks' => $newBlocks = ['_someid' => 'data', '_someid2' => 'data'],
            'order' => $order = ['_someid2', '_someid'],
        ];

        $operation = $this->denormalizer->denormalize($data, OperationInterface::class);
        $this->assertInstanceOf(CreateOperation::class, $operation);
        $this->assertSame($newBlocks, $operation->getNewBlockDefinitions());
        $this->assertSame($order, $operation->getBlockOrder());
    }

    public function testDenormalizeCreateWithEmptyContentId()
    {
        $data = [
            'contentId' => false,
            'blocks' => [],
            'newBlocks' => $newBlocks = ['_someid' => 'data', '_someid2' => 'data'],
            'order' => $order = ['_someid2', '_someid'],
        ];

        $operation = $this->denormalizer->denormalize($data, OperationInterface::class);
        $this->assertInstanceOf(CreateOperation::class, $operation);
        $this->assertSame($newBlocks, $operation->getNewBlockDefinitions());
        $this->assertSame($order, $operation->getBlockOrder());
    }

    public function testDenormalizeUpdate()
    {
        $data = [
            'contentId' => 'some-guid-1111',
            'blocks' => $blocks = ['id1' => 'data', 'id2' => 'data'],
            'newBlocks' => $newBlocks = ['_someid' => 'data', '_someid2' => 'data'],
            'order' => $order = ['_someid2', '_someid'],
        ];

        $content = new Content();
        $this->repo->expects($this->any())
            ->method('find')
            ->with('some-guid-1111')
            ->will($this->returnValue($content));

        $operation = $this->denormalizer->denormalize($data, OperationInterface::class);
        $this->assertInstanceOf(UpdateOperation::class, $operation);
        $this->assertSame($content, $operation->getContent());
        $this->assertSame($blocks, $operation->getBlockDefinitions());
        $this->assertSame($newBlocks, $operation->getNewBlockDefinitions());
        $this->assertSame($order, $operation->getBlockOrder());
    }

    public function testMissingDataThrowsException()
    {
        $data = [
            'newBlocks' => ['_someid' => 'data', '_someid2' => 'data'],
            'order' => ['_someid2', '_someid'],
        ];
        $this->setExpectedException(InvalidArgumentException::class);
        $this->denormalizer->denormalize($data, OperationInterface::class);
    }

    public function testUnknownContentEntityThrowsException()
    {
        $data = [
            'contentId' => 'some-unknown-guid-1111',
            'blocks' => ['id1' => 'data', 'id2' => 'data'],
            'newBlocks' => ['_someid' => 'data', '_someid2' => 'data'],
            'order' => ['_someid2', '_someid'],
        ];
        $this->repo->expects($this->any())
            ->method('find')
            ->will($this->returnValue(null));
        $this->setExpectedException(NotNormalizableValueException::class);
        $this->denormalizer->denormalize($data, OperationInterface::class);
    }

    public function testBlockDefinitionsWithNoContentIdThrowsException()
    {
        $data = [
            'blocks' => ['id1' => 'data', 'id2' => 'data'],
            'newBlocks' => ['_someid' => 'data', '_someid2' => 'data'],
            'order' => ['_someid2', '_someid'],
        ];
        $this->setExpectedException(NotNormalizableValueException::class);
        $this->denormalizer->denormalize($data, OperationInterface::class);
    }
}
