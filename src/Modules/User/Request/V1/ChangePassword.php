<?php

declare(strict_types=1);

namespace App\Modules\User\Request\V1;

use App\Modules\User\Command\Sync\ChangePassword as Command;
use App\Shared\Request\RequestInterface;

final readonly class ChangePassword implements RequestInterface
{
    public function __construct(
        public mixed $email,
        public mixed $password,
        public mixed $token,
    ) {
    }

    public function toCommand(): Command
    {
        return new Command(
            $this->email,
            $this->password,
            $this->token,
        );
    }
}
