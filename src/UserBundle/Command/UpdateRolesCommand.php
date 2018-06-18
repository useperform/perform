<?php

namespace Perform\UserBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Perform\UserBundle\Entity\User;
use Symfony\Component\Console\Question\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Perform\BaseBundle\Doctrine\EntityResolver;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class UpdateRolesCommand extends Command
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
        $this->setName('perform:user:update-roles')
            ->setDescription('Update the security roles for a user')
            ->addArgument('email', InputArgument::OPTIONAL, 'Email address of the user.')
            ->addOption('remove', 'r', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Roles to remove from the user.')
            ->addOption('add', 'a', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Roles to add to the user.')
            ->addOption('show', 's', InputOption::VALUE_NONE, 'Just show the roles of the user.')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = $this->getUser($input, $output);

        $output->writeln([
            sprintf('Current roles for <info>%s</info>:', $user->getEmail()),
            '',
            implode($user->getRoles(), ' '),
        ]);
        if ($input->getOption('show')) {
            return;
        }

        $addRoles = $this->getRolesToAdd($input, $output);
        $removeRoles = $this->getRolesToRemove($input, $output);

        $output->writeln('');
        foreach ($addRoles as $role) {
            $user->addRole($role);
            $output->writeln(sprintf('Adding role <info>%s</info>', $role));
        }
        foreach ($removeRoles as $role) {
            $user->removeRole($role);
            $output->writeln(sprintf('Removing role <info>%s</info>', $role));
        }

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln([
            '',
            sprintf('Updated roles for user <info>%s</info>, email <info>%s</info>.', $user->getFullname(), $user->getEmail()),
        ]);
    }

    protected function getUser(InputInterface $input, OutputInterface $output)
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

        return $user;
    }

    protected function sanitizeRoles(array $roles)
    {
        $cleaned = [];
        foreach ($roles as $role) {
            if (!trim($role)) {
                continue;
            }

            $cleaned[] = trim(strtoupper($role));
        }

        return $cleaned;
    }

    protected function getRolesToAdd(InputInterface $input, OutputInterface $output)
    {
        if (!empty($input->getOption('add'))) {
            return $this->sanitizeRoles($input->getOption('add'));
        }

        //if --remove has been passed, don't prompt
        if (!empty($input->getOption('remove'))) {
            return [];
        }
        $output->writeln([
            '',
            'Enter the roles to add.',
            'Separate multiple roles with a comma, e.g. ROLE_ADMIN, ROLE_MANAGER',
            '',
        ]);

        $question = new Question('Roles to add (leave empty to skip): ');
        $question->setValidator(function ($string) {
            return explode(',', $string);
        });

        $helper = $this->getHelper('question');

        return $this->sanitizeRoles($helper->ask($input, $output, $question));
    }

    protected function getRolesToRemove(InputInterface $input, OutputInterface $output)
    {
        if (!empty($input->getOption('remove'))) {
            return $this->sanitizeRoles($input->getOption('remove'));
        }

        //if --add has been passed, don't prompt
        if (!empty($input->getOption('add'))) {
            return [];
        }
        $output->writeln([
            '',
            'Enter the roles to remove.',
            'Separate multiple roles with a comma, e.g. ROLE_ADMIN, ROLE_MANAGER',
            '',
        ]);

        $question = new Question('Roles to remove (leave empty to skip): ');
        $question->setValidator(function ($string) {
            return explode(',', strtoupper($string));
        });

        $helper = $this->getHelper('question');

        return $this->sanitizeRoles($helper->ask($input, $output, $question));
    }
}
