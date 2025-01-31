<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Modules\User\Entity\User;
use App\Modules\User\Enum\TokenType;
use App\Modules\User\Exception\EmailAlreadyUsed;
use App\Modules\User\Facade\UserTokenFacade;
use App\Modules\User\Repository\UserRepository;
use App\Shared\Command\Sync\CommandHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class ChangeUserEmailHandler implements CommandHandler
{
    public function __construct(
        private UserTokenFacade $userTokenFacade,
        private UserRepository $userRepository,
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    public function __invoke(ChangeUserEmail $command): void
    {
        if ($this->userRepository->findByEmail($command->email) !== null) {
            throw new EmailAlreadyUsed();
        }

        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $user->setEmail($command->email)
            ->setIsVerified(false);

        $this->userRepository->save($user);

        $this->userTokenFacade->createAndSendVerificationToken($user, TokenType::EMAIL_VERIFICATION);
    }
}
