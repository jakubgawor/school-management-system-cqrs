<?php

declare(strict_types=1);

namespace App\Modules\User\Service;

use App\Modules\User\Entity\User;
use App\Modules\User\Exception\UserAlreadyExistsException;
use App\Modules\User\Factory\UserRegisterFactory;
use App\Modules\User\Repository\UserRepository;

final class UserRegistrationService
{
    public function __construct(
        private UserRepository $userRepository,
        private UserRegisterFactory $userRegisterFactory,
    ) {
    }

    public function registerUser(string $email, string $password): User
    {
        if ($this->userRepository->findByEmail($email) !== null) {
            throw new UserAlreadyExistsException();
        }

        $user = $this->userRegisterFactory->create($email, $password);

        $this->userRepository->save($user);

        return $user;
    }
}
