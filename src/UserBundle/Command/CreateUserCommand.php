<?php

namespace Perform\UserBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Perform\UserBundle\Entity\User;
use Symfony\Component\Console\Question\Question;
use Doctrine\ORM\EntityManagerInterface;

/**
 * CreateUserCommand.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class CreateUserCommand extends ContainerAwareCommand
{
    protected $name = 'perform:user:create';
    protected $description = 'Create a new user';

    protected function configure()
    {
        $this->setName($this->name)
            ->setDescription($this->description)
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $resolver = $this->getContainer()->get('perform_base.doctrine.entity_resolver');
        $userClass = $resolver->resolve('PerformUserBundle:User');
        $this->checkUserTable($em, $userClass);

        $helper = $this->getHelper('question');
        $user = new $userClass();
        $user->setForename($helper->ask($input, $output, new Question('Forename: ')));
        $user->setSurname($helper->ask($input, $output, new Question('Surname: ')));
        $user->setEmail($helper->ask($input, $output, new Question('Email: ')));

        $question = new Question('Password: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $user->setPlainPassword($helper->ask($input, $output, $question));

        $em->persist($user);
        $em->flush();

        $output->writeln(sprintf('Created user <info>%s</info>, email <info>%s</info>.', $user->getFullname(), $user->getEmail()));
    }

    protected function checkUserTable(EntityManagerInterface $em, $userClass)
    {
        $repo = $em->getRepository($userClass);
        try {
            $repo->findOneBy([]);
        } catch (\Exception $e) {
            $msg = 'Unable to find the user database table.'
                 .PHP_EOL
                 .'Be sure to configure the database connection in app/config/config.yml and app/config/parameters.yml, and create the user table with either the doctrine:schema:update command or database migrations.';
            throw new \RuntimeException($msg);
        }
    }
}
