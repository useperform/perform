<?php

namespace Perform\UserBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Perform\UserBundle\Entity\User;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Doctrine\ORM\EntityManagerInterface;
use Perform\BaseBundle\Doctrine\EntityResolver;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class ResetPasswordCommand extends Command
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
        $this->setName('perform:user:reset-password')
            ->setDescription('Reset a password')
            ->addArgument('email', InputArgument::OPTIONAL, 'Email address of the user')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        if (!$email) {
            $question = new Question('User email: ');
            $email = $this->getHelper('question')->ask($input, $output, $question);
        }

        $user = $this->em->getRepository($this->resolver->resolve('PerformUserBundle:User'))->findOneByEmail($email);

        if (!$user) {
            throw new \RuntimeException(sprintf('User with email "%s" was not found.', $email));
        }

        $helper = $this->getHelper('question');
        $question = new Question('New password: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $user->setPlainPassword($helper->ask($input, $output, $question));

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln(sprintf('Updated password for user <info>%s</info>, email <info>%s</info>.', $user->getFullname(), $user->getEmail()));
    }
}
