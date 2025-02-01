<?php

declare(strict_types=1);

namespace App\Modules\User\Facade;

use App\Modules\User\Repository\UserRepository;

final class UserFacade
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function getAllUserEmails(): array
    {
        return $this->userRepository->getAllUserEmails();
    }
}
