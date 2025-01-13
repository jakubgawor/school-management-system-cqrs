<?php

declare(strict_types=1);

namespace App\Modules\User\Util;

use App\Modules\User\Entity\User;
use App\Modules\User\Entity\UserVerificationToken;
use App\Modules\User\Enum\TokenType;
use App\Modules\User\Exception\TokenDoesNotExists;
use App\Modules\User\Exception\TokenExpired;
use App\Modules\User\Repository\UserRepository;
use App\Modules\User\Repository\UserVerificationTokenRepository;
use App\Modules\User\Service\UserFetcherService;

abstract class AbstractTokenHandler
{
    public function __construct(
        protected UserRepository $userRepository,
        protected UserVerificationTokenRepository $userVerificationTokenRepository,
        protected UserFetcherService $userFetcherService,
    ) {
    }

    protected function getUserOrFail(string $email, bool $mustBeNotVerified = false): User
    {
        if ($mustBeNotVerified) {
            $user = $this->userFetcherService->getNotVerifiedByEmailOrFail($email);
        } else {
            $user = $this->userFetcherService->getByEmailOrFail($email);
        }

        return $user;
    }

    protected function getValidTokenOrFail(string $email, string $token, TokenType $type): UserVerificationToken
    {
        $token = $this->userVerificationTokenRepository->findValidToken($email, $token, $type);

        if (! $token) {
            throw new TokenDoesNotExists();
        }

        if ($token->isExpired() === true) {
            throw new TokenExpired();
        }

        return $token;
    }
}
