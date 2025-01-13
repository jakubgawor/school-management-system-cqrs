<?php

declare(strict_types=1);

namespace App\Modules\User\Service;

use App\Modules\User\Entity\User;
use App\Modules\User\Guard\UserExistsGuard;
use App\Modules\User\Repository\UserRepository;

final class UserFetcherService
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function getByIdOrFail(string $id): User
    {
        $user = $this->userRepository->findById($id);
        UserExistsGuard::guard($user);

        return $user;
    }

    public function getByEmailOrFail(string $email): User
    {
        $user = $this->userRepository->findByEmail($email);
        UserExistsGuard::guard($user);

        return $user;
    }

    public function getNotVerifiedByEmailOrFail(string $email): User
    {
        $user = $this->userRepository->findNotVerifiedByEmail($email);
        UserExistsGuard::guard($user);

        return $user;
    }
}
