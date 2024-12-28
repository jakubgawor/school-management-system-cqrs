<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Modules\User\Entity\User;
use App\Modules\User\Exception\UserAlreadyExistsException;
use App\Modules\User\Repository\UserRepository;
use App\Shared\Command\Sync\CommandHandler;
use App\Shared\Ramsey\IdGenerator;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

final class UserRegisterHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordHasherInterface $passwordHasher,
    ) {
    }

    public function __invoke(UserRegister $command): void
    {
        if ($this->userRepository->findByEmail($command->email) !== null) {
            throw new UserAlreadyExistsException();
        }

        $user = new User(
            IdGenerator::generate(),
            $command->email,
            $this->passwordHasher->hash($command->password),
        );

        $this->userRepository->save($user);
    }
}
