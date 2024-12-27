<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Modules\User\Entity\User;
use App\Modules\User\Exception\UserAlreadyExistsException;
use App\Modules\User\Repository\UserRepository;
use App\Shared\Command\Sync\CommandHandler;
use App\Shared\Ramsey\IdGenerator;

final class UserRegisterHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(UserRegister $command): void
    {
        if ($this->userRepository->findByUsername($command->username) !== null) {
            throw new UserAlreadyExistsException();
        }

        $user = new User(
            IdGenerator::generate(),
            $command->username,
            $command->password
        );

        $this->userRepository->save($user);
    }
}
