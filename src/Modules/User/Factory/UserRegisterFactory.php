<?php

declare(strict_types=1);

namespace App\Modules\User\Factory;

use App\Modules\User\Entity\User;
use App\Shared\Ramsey\IdGenerator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserRegisterFactory
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function create(string $email, string $password): User
    {
        $user = new User();
        $user->setId(IdGenerator::generate());
        $user->setEmail($email);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));

        return $user;
    }
}
