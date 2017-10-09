<?php

namespace Perform\UserBundle\Tests\Importer;

use Perform\UserBundle\Importer\UserImporter;
use Perform\UserBundle\Entity\User;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UserImporterTest extends \PHPUnit_Framework_TestCase
{
    protected $entityManager;
    protected $repo;
    protected $importer;

    public function setUp()
    {
        $this->entityManager = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $this->repo = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->entityManager->expects($this->any())
            ->method('getRepository')
            ->with('PerformUserBundle:User')
            ->will($this->returnValue($this->repo));
        $this->importer = new UserImporter($this->entityManager);
    }

    public function testImportNewUser()
    {
        $user = new User();
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($user);
        $this->entityManager->expects($this->once())
            ->method('flush');
        $this->importer->import($user);
    }

    public function testExistingUserStopsImport()
    {
        $existing = new User();
        $existing->setEmail('test@example.com');
        $new = new User();
        $new->setEmail('test@example.com');
        $this->entityManager->expects($this->never())
            ->method('persist');
        $this->entityManager->expects($this->never())
            ->method('flush');
        $this->repo->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'test@example.com'])
            ->will($this->returnValue($existing));

        $this->importer->import($new);
    }

    public function testParseYamlFile()
    {
        $users = $this->importer->parseYamlFile(__DIR__.'/sample_users.yml');
        $this->assertSame(2, count($users));

        $one = $users[0];
        $this->assertSame('test@example.com', $one->getEmail());
        $this->assertSame('Test', $one->getForename());
        $this->assertSame('User', $one->getSurname());
        $this->assertSame('some_bcrypt_hash', $one->getPassword());

        $two = $users[1];
        $this->assertSame('test@example.co.uk', $two->getEmail());
        $this->assertSame('Test2', $two->getForename());
        $this->assertSame('User2', $two->getSurname());
        $this->assertSame('some_other_bcrypt_hash', $two->getPassword());
    }

    public function testImportYamlFile()
    {
        $this->entityManager->expects($this->exactly(2))
            ->method('persist');
        $this->entityManager->expects($this->exactly(2))
            ->method('flush');
        $this->importer->importYamlFile(__DIR__.'/sample_users.yml');
    }
}
