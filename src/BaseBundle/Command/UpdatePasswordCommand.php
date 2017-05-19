<?php

namespace Perform\BaseBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Perform\BaseBundle\Entity\User;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputArgument;

class UpdatePasswordCommand extends ContainerAwareCommand
{
    protected $name = 'perform:user:update-password';
    protected $description = 'Update a user password';

    protected function configure()
    {
        $this->setName($this->name)
            ->setDescription($this->description)
            ->addArgument('email', InputArgument::OPTIONAL, 'Email address of the user.')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        if (!$email) {
            $question = new Question('User email: ');
            $email = $this->getHelper('question')->ask($input, $output, $question);
        }

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('PerformBaseBundle:User')->findOneByEmail($email);

        if (!$user) {
            throw new \RuntimeException(sprintf('User <info>%s</info> not found.', $email));
        }

        $helper = $this->getHelper('question');
        $question = new Question('New password: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $user->setPlainPassword($helper->ask($input, $output, $question));

        $em->persist($user);
        $em->flush();

        $output->writeln(sprintf('Updated password for user <info>%s</info>, email <info>%s</info>.', $user->getFullname(), $user->getEmail()));
    }
}
