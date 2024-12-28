<?php

declare(strict_types=1);

namespace App\Modules\User\Service;

use App\Modules\User\Entity\User;
use App\Modules\User\Entity\UserVerificationToken;
use App\Modules\User\Repository\UserVerificationTokenRepository;
use App\Shared\Ramsey\IdGenerator;

final class UserVerificationTokenService
{
    public function __construct(
        private UserVerificationTokenRepository $userVerificationTokenRepository,
    ) {
    }

    public function createVerificationToken(User $user): UserVerificationToken
    {
        $verificationToken = new UserVerificationToken(
            IdGenerator::generate(),
            $user,
            $this->createTokenString(),
        );

        $this->userVerificationTokenRepository->save($verificationToken);

        return $verificationToken;
    }

    private function createTokenString(): string
    {
        return bin2hex(random_bytes(32));
    }
}
