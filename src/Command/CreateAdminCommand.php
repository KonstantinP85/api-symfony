<?php

declare(strict_types=1);

namespace App\Command;

use App\DataProvider\UserDataProvider;
use App\Manager\UserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateAdminCommand extends Command
{
    /**
     * @var UserManager
     */
    private UserManager $userManager;

    /**
     * CreateAdminCommand constructor.
     * @param UserManager $userManager
     * @param string|null $name
     */
    public function __construct(UserManager $userManager, string $name = null)
    {
        parent::__construct($name);
        $this->userManager = $userManager;
    }

    protected function configure(): void
    {
        $this->setName('app:create-admin');
        $definition = [
            new InputArgument('firstName', InputArgument::REQUIRED, 'First name'),
            new InputArgument('lastName', InputArgument::REQUIRED, 'Last name'),
            new InputArgument('patronymic', InputArgument::REQUIRED, 'Patronymic'),
            new InputArgument('email', InputArgument::REQUIRED, 'E-mail'),
            new InputArgument('phone', InputArgument::REQUIRED, 'Phone number'),
            new InputArgument('password', InputArgument::REQUIRED, 'Password')
        ];
        $this->setDefinition($definition);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Create Admin');

        if (!$input->getArgument('firstName')) {
            do {
                $firstName = $io->ask('First name');
                if (empty($firstName)) {
                    $io->error('First name not be empty');
                }
            } while (empty($firstName));
            $input->setArgument('firstName', $firstName);
        }
        if (!$input->getArgument('lastName')) {
            do {
                $lastName = $io->ask('Last name');
                if (empty($lastName)) {
                    $io->error('Last name not be empty');
                }
            } while (empty($lastName));
            $input->setArgument('lastName', $lastName);
        }
        if (!$input->getArgument('email')) {
            do {
                $email = $io->ask('Email');
                if (empty($email)) {
                    $io->error('Email not be empty');
                }
            } while (empty($email));
            $input->setArgument('email', $email);
        }
        if (!$input->getArgument('phone')) {
            do {
                $phone = $io->ask('Phone');
                if (empty($phone)) {
                    $io->error('Phone not be empty');
                }
            } while (empty($phone));
            $input->setArgument('phone', $phone);
        }
        if (!$input->getArgument('patronymic')) {
            do {
                $patronymic = $io->ask('Patronymic');
                if (empty($patronymic)) {
                    $io->error('Patronymic not be empty');
                }
            } while (empty($patronymic));
            $input->setArgument('patronymic', $patronymic);
        }
        if (!$input->getArgument('password')) {
            do {
                $password = $io->ask('Password');
                if (empty($password)) {
                    $io->error('Password not be empty');
                }
            } while (empty($password));
            $input->setArgument('password', $password);
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $firstName = $input->getArgument('firstName');
        $lastName = $input->getArgument('lastName');
        $email = $input->getArgument('email');
        $phone = $input->getArgument('phone');
        $patronymic = $input->getArgument('patronymic');
        $password = $input->getArgument('password');
        try {
            $this->userManager->create(
                $firstName,
                $lastName,
                [UserDataProvider::ROLE_ADMIN],
                $email,
                $phone,
                $password,
                $patronymic
            );
            $io->success('Admin was created');
        } catch (\Exception $e) {
            $io->error('Error create admin: ' . $e->getMessage());
            return -1;
        }
        return 0;
    }
}