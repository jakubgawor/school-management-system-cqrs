<?php

declare(strict_types=1);

namespace App\Modules\User\Request\V1;

use App\Modules\User\Command\Sync\UserRegister as Command;
use App\Shared\Request\RequestInterface;

final readonly class UserRegister implements RequestInterface
{
    public function __construct(
        public mixed $firstName,
        public mixed $lastName,
        public mixed $email,
        public mixed $password,
        public mixed $confirmPassword,
    ) {
    }

    public function toCommand(): Command
    {
        return new Command(
            $this->firstName,
            $this->lastName,
            $this->email,
            $this->password,
            $this->confirmPassword,
        );
    }
}
