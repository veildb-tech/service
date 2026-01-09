<?php

namespace App\Command\Admin;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Admin\User;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand('app:admin:user-create', 'Create admin user')]
class CreateUser extends Command
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param string|null $name
     */
    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        string                                       $name = null
    ) {
        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this->addOption(
            'email',
            null,
            InputOption::VALUE_REQUIRED
        );

        $this->addOption(
            'password',
            null,
            InputOption::VALUE_REQUIRED
        );

        $this->addOption(
            'firstname',
            null,
            InputOption::VALUE_REQUIRED
        );

        $this->addOption(
            'lastname',
            null,
            InputOption::VALUE_REQUIRED
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User();

        $user->setEmail($input->getOption('email'))
            ->setFirstname($input->getOption('firstname'))
            ->setLastname($input->getOption('lastname'))
            ->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $input->getOption('password')
                )
            );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return Command::SUCCESS;

    }
}
