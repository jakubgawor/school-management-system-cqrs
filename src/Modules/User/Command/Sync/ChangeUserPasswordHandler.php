<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Modules\User\Exception\PasswordsDoNotMatch;
use App\Modules\User\Guard\PasswordsMatchGuard;
use App\Modules\User\Repository\UserRepository;
use App\Shared\Command\Sync\CommandHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class ChangeUserPasswordHandler implements CommandHandler
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private UserPasswordHasherInterface $userPasswordHasher,
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(ChangeUserPassword $command): void
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if (! $this->userPasswordHasher->isPasswordValid($user, $command->currentPassword)) {
            throw new PasswordsDoNotMatch();
        }

        PasswordsMatchGuard::guard($command->newPassword, $command->newPasswordConfirmation);

        $user->setPassword($this->userPasswordHasher->hashPassword($user, $command->newPassword));
        $this->userRepository->save($user);
    }
}
