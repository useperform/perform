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

    public function import(User $user)
    {
        $existing = $this->find($user->getEmail());
        if ($existing) {
            return;
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function importYamlFile($path)
    {
        foreach ($this->parseYamlFile($path) as $user) {
            $this->import($user);
        }
    }

    protected function find($email)
    {
        return $this->repo->findOneBy(['email' => $email]);
    }

    public function parseYamlFile($path)
    {
        $config = Yaml::parse(file_get_contents($path));

        foreach ($config as $email => $definition) {
            $collection[] = $this->newUser($email, $definition);
        }

        return $collection;
    }

    protected function newUser($email, array $definition)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired('forename')
            ->setRequired('surname')
            ->setRequired('password')
            ->setDefault('roles', [])
            ->setAllowedTypes('roles', 'array');
        $definition = $resolver->resolve($definition);

        $user = new User();
        $user->setEmail($email)
            ->setForename($definition['forename'])
            ->setSurname($definition['surname'])
            ->setPassword($definition['password']);
        foreach ($definition['roles'] as $role) {
            $user->addRole($role);
        }

        return $user;
    }
}
