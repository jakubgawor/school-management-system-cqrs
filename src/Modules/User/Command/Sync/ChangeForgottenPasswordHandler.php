<?php

declare(strict_types=1);

namespace App\Modules\User\Command\Sync;

use App\Modules\User\Enum\TokenType;
use App\Modules\User\Guard\PasswordsMatchGuard;
use App\Modules\User\Repository\UserRepository;
use App\Modules\User\Repository\UserVerificationTokenRepository;
use App\Modules\User\Service\UserFetcherService;
use App\Modules\User\Util\AbstractTokenHandler;
use App\Shared\Command\Sync\CommandHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class ChangeForgottenPasswordHandler extends AbstractTokenHandler implements CommandHandler
{
    public function __construct(
        UserRepository $userRepository,
        UserVerificationTokenRepository $userVerificationTokenRepository,
        UserFetcherService $userFetcherService,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct($userRepository, $userVerificationTokenRepository, $userFetcherService);
    }

    public function __invoke(ChangeForgottenPassword $command): void
    {
        PasswordsMatchGuard::guard($command->password, $command->repeatPassword);

        $user = $this->getUserOrFail($command->email);
        $token = $this->getValidTokenOrFail($command->email, $command->token, TokenType::PASSWORD_RESET);

        $user->setPassword($this->passwordHasher->hashPassword($user, $command->password));
        $this->userRepository->save($user);

        $token->invalidateToken();
        $this->userVerificationTokenRepository->save($token);
    }
}
