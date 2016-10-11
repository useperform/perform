<?php

namespace Perform\BaseBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Perform\BaseBundle\Entity\User;
use Symfony\Component\Console\Question\Question;

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
        $helper = $this->getHelper('question');
        $user = new User();
        $user->setForename($helper->ask($input, $output, new Question('Forename: ')));
        $user->setSurname($helper->ask($input, $output, new Question('Surname: ')));
        $user->setEmail($helper->ask($input, $output, new Question('Email: ')));

        $question = new Question('Password: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $user->setPlainPassword($helper->ask($input, $output, $question));

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $em->persist($user);
        $em->flush();

        $output->writeln(sprintf('Created user <info>%s</info>, email <info>%s</info>.', $user->getFullname(), $user->getEmail()));
    }
}
