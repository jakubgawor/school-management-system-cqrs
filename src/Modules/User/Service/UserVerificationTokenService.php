<?php

declare(strict_types=1);

namespace App\Modules\User\Service;

use App\Modules\User\Entity\User;
use App\Modules\User\Entity\UserVerificationToken;
use App\Modules\User\Enum\TokenType;
use App\Modules\User\Repository\UserVerificationTokenRepository;
use App\Shared\Ramsey\IdGenerator;

final class UserVerificationTokenService
{
    public function __construct(
        private UserVerificationTokenRepository $userVerificationTokenRepository,
    ) {
    }

    public function createVerificationToken(User $user, TokenType $tokenType): UserVerificationToken
    {
        $verificationToken = new UserVerificationToken(
            IdGenerator::generate(),
            $user,
            $this->createTokenString(),
            $tokenType
        );

        $this->userVerificationTokenRepository->save($verificationToken);

        return $verificationToken;
    }

    private function createTokenString(): string
    {
        return str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
    }
}
