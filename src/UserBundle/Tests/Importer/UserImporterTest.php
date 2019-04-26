<?php

namespace Perform\UserBundle\Tests\Importer;

use PHPUnit\Framework\TestCase;
use Perform\UserBundle\Importer\UserImporter;
use Perform\UserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UserImporterTest extends TestCase
{
    protected $entityManager;
    protected $repo;
    protected $importer;

    public function setUp()
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->repo = $this->createMock(ObjectRepository::class);
        $this->entityManager->expects($this->any())
            ->method('getRepository')
            ->with('PerformUserBundle:User')
            ->will($this->returnValue($this->repo));
        $this->importer = new UserImporter($this->entityManager);
    }

    public function testImport()
    {
        $defs = [
            [
                'email' => 'user@example.com',
                'forename' => 'Test',
                'surname' => 'User',
                'password' => 'some_pass_hash',
                'roles' => [],
            ],
        ];
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($user) {
                return $user instanceof User
                    && $user->getEmail() === 'user@example.com'
                    && $user->getForename() === 'Test'
                    && $user->getSurname() === 'User'
                    && $user->getPassword() === 'some_pass_hash'
                    && $user->getRoles() === ['ROLE_USER']
                    ;
            }));
        $this->entityManager->expects($this->once())
            ->method('flush');
        $this->importer->import($defs);
    }

    public function testImportExistingUser()
    {
        $this->entityManager->expects($this->never())
            ->method('persist');
        $this->entityManager->expects($this->never())
            ->method('flush');
        $this->repo->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'test@example.com'])
            ->will($this->returnValue(new User()));

        $this->importer->import([
            [
                'email' => 'test@example.com',
            ],
        ]);
    }

    public function testImportWithRoles()
    {
        $defs = [
            [
                'email' => 'user@example.com',
                'forename' => 'Test',
                'surname' => 'User',
                'password' => 'some_pass_hash',
                'roles' => ['ROLE_ADMIN'],
            ],
        ];
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($user) {
                return $user instanceof User
                    && $user->getRoles() === ['ROLE_USER', 'ROLE_ADMIN']
                    ;
            }));
        $this->importer->import($defs);
    }
}
