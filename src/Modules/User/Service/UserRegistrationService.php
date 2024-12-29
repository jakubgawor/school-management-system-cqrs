<?php

declare(strict_types=1);

namespace App\Modules\User\Service;

use App\Modules\User\Entity\User;
use App\Modules\User\Exception\UserAlreadyExists;
use App\Modules\User\Repository\UserRepository;
use App\Shared\Ramsey\IdGenerator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserRegistrationService
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function registerUser(string $email, string $password): User
    {
        if ($this->userRepository->findByEmail($email) !== null) {
            throw new UserAlreadyExists();
        }

        $user = new User();
        $user->setId(IdGenerator::generate());
        $user->setEmail($email);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));

        $this->userRepository->save($user);

        return $user;
    }
}
