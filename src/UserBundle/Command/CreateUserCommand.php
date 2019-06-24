<?php

namespace Perform\UserBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Perform\UserBundle\Entity\User;
use Symfony\Component\Console\Question\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Perform\BaseBundle\Doctrine\EntityResolver;
use Symfony\Component\Console\Input\InputOption;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class CreateUserCommand extends Command
{
    protected $em;
    protected $resolver;

    public function __construct(EntityManagerInterface $em, EntityResolver $resolver)
    {
        $this->em = $em;
        $this->resolver = $resolver;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('perform:user:create')
            ->setDescription('Create a new user account in the database')
            ->addOption('require-new-password', null, InputOption::VALUE_NONE, 'Require the user to reset their password on login');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userClass = $this->resolver->resolve('PerformUserBundle:User');
        // check for a bad database early to not waste time asking questions
        $this->checkUserTable($userClass);

        $helper = $this->getHelper('question');
        $user = new $userClass();
        $user->setForename($helper->ask($input, $output, new Question('Forename: ')));
        $user->setSurname($helper->ask($input, $output, new Question('Surname: ')));
        $user->setEmail($helper->ask($input, $output, new Question('Email: ')));

        $question = new Question('Password: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $user->setPlainPassword($helper->ask($input, $output, $question));

        $user->setPasswordExpiresAt($input->getOption('require-new-password') ? new \DateTime('-1 second') : new \DateTime('+1 year'));

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln(sprintf('Created user <info>%s</info>, email <info>%s</info>.', $user->getFullname(), $user->getEmail()));
    }

    protected function checkUserTable($userClass)
    {
        $repo = $this->em->getRepository($userClass);
        try {
            $repo->findOneBy([]);
        } catch (\Exception $e) {
            $msg = 'Unable to find the user database table.'
                 .PHP_EOL
                 .'Be sure to configure a valid database connection and create the user table with either the doctrine:schema:update command or database migrations.';
            throw new \RuntimeException($msg);
        }
    }
}
