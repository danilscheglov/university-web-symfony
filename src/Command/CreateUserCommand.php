<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

// the name of the command is what users type after "php bin/console"
#[AsCommand(
    name: 'app:user:create',
    description: 'Creates a new user.'
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ){
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $user = new User();

        $user->setUsername($io->ask('Username'))
            ->setEmail($io->ask('Email'))
            ->setPassword($io->askHidden('Password'))
            ->setRole(strtolower($io->choice('Role', [1 => 'User', 2 => 'Admin'], 1)));

        $this->userRepository->save($user);

        return Command::SUCCESS;
    }
}