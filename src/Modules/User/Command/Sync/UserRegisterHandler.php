<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Modules\User\Exception\UserAlreadyExistsException;
use App\Modules\User\Factory\UserRegisterFactory;
use App\Modules\User\Repository\UserRepository;
use App\Shared\Command\Sync\CommandHandler;

final class UserRegisterHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserRegisterFactory $userRegisterFactory,
    ) {
    }

    public function __invoke(UserRegister $command): void
    {
        if ($this->userRepository->findByEmail($command->email) !== null) {
            throw new UserAlreadyExistsException();
        }

        $user = $this->userRegisterFactory->create(
            $command->email,
            $command->password
        );

        $this->userRepository->save($user);
    }
}
