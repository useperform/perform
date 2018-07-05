<?php

namespace Perform\UserBundle\Importer;

use Symfony\Component\Yaml\Yaml;
use Perform\UserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UserImporter
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repo = $entityManager->getRepository('PerformUserBundle:User');
    }

    public function import(array $definitions)
    {
        $newUsers = [];
        foreach ($definitions as $definition) {
            $user = $this->importUser($definition);
            if ($user) {
                $newUsers[] = $user;
            }
        }

        if (!empty($newUsers)) {
            $this->entityManager->flush();
        }

        return $newUsers;
    }

    protected function importUser(array $definition)
    {
        if (!isset($definition['email'])) {
            throw new \InvalidArgumentException('Missing required "email" property for importing a user.`');
        }

        $existing = $this->repo->findOneBy(['email' => $definition['email']]);
        if ($existing instanceof User) {
            return;
        }

        $user = $this->newUser($definition);
        $this->entityManager->persist($user);

        return $user;
    }

    protected function newUser(array $definition)
    {
        $user = new User();
        $user->setEmail($definition['email'])
            ->setForename($definition['forename'])
            ->setSurname($definition['surname'])
            ->setPassword($definition['password']);
        foreach ($definition['roles'] as $role) {
            $user->addRole($role);
        }

        return $user;
    }
}
