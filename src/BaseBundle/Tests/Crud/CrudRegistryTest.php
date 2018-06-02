<?php

namespace Perform\BaseBundle\Tests\Crud;

use Perform\BaseBundle\Crud\CrudRegistry;
use Perform\UserBundle\Entity\User;
use Perform\BaseBundle\Doctrine\EntityResolver;
use Perform\BaseBundle\Crud\CrudInterface;
use Perform\BaseBundle\Crud\CrudNotFoundException;
use Perform\BaseBundle\Test\Services;
use Perform\BaseBundle\Crud\DuplicateCrudException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudRegistryTest extends \PHPUnit_Framework_TestCase
{
    protected $crudOne;
    protected $crudTwo;
    protected $registry;

    public function setUp()
    {
        $this->em = $this->getMock(EntityManagerInterface::class);
        $this->crudOne = $this->getMock(CrudInterface::class);
        $this->crudTwo = $this->getMock(CrudInterface::class);
        $cruds = Services::serviceLocator([
            'one' => $this->crudOne,
            'two' => $this->crudTwo,
        ]);
        $entityMap = [
            'Entity\One' => ['one'],
            'Entity\Two' => ['two'],
        ];
        $this->registry = new CrudRegistry(new EntityResolver([]), $this->em, $cruds, $entityMap);
    }

    public function testGet()
    {
        $this->assertSame($this->crudOne, $this->registry->get('one'));
        $this->assertSame($this->crudTwo, $this->registry->get('two'));
    }

    public function testGetUnknown()
    {
        $this->setExpectedException(CrudNotFoundException::class);
        $this->registry->get('unknown');
    }

    public function testGetInvalidArgument()
    {
        $this->setExpectedException(CrudNotFoundException::class);
        $this->registry->get(false);
    }

    public function testHas()
    {
        $this->assertTrue($this->registry->has('one'));
        $this->assertFalse($this->registry->has('unknown'));
    }

    public function testGetNameForEntity()
    {
        $this->assertSame('one', $this->registry->getNameForEntity('Entity\One'));
    }

    public function testGetAllNamesForEntity()
    {
        $cruds = Services::serviceLocator([
            'one' => $this->crudOne,
            'two' => $this->crudTwo,
        ]);
        $entityMap = [
            'Entity\Item' => ['one', 'two'],
        ];
        $registry = new CrudRegistry(new EntityResolver([]), $this->em, $cruds, $entityMap);

        $this->assertSame(['one', 'two'], $registry->getAllNamesForEntity('Entity\Item'));
    }

    public function testGetNameForEntityWithMultipleThrowsException()
    {
        $cruds = Services::serviceLocator([
            'one' => $this->crudOne,
            'two' => $this->crudTwo,
        ]);
        $entityMap = [
            'Entity\Item' => ['one', 'two'],
        ];
        $registry = new CrudRegistry(new EntityResolver([]), $this->em, $cruds, $entityMap);

        $this->setExpectedException(DuplicateCrudException::class);
        $registry->getNameForEntity('Entity\Item');
    }
}
