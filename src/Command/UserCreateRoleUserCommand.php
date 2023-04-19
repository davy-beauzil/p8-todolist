<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:user:create-role-user', description: 'Add a short description for your command',)]
class UserCreateRoleUserCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var User[] $users */
        $users = $this->userRepository->findAll();

        foreach ($users as $user) {
            if ($user->getRoles() === []) {
                $user->setRoles(['ROLE_USER']);
                $this->userRepository->save($user);
            }
        }

        $io->success('All users without role have been updated with "ROLE_USER".');
        return Command::SUCCESS;
    }
}
