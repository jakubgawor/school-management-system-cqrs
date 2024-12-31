<?php

declare(strict_types=1);

namespace App\Modules\User\Request\V1;

use App\Modules\User\Command\Sync\ResendVerificationCode as Command;
use App\Modules\User\Enum\TokenType;
use App\Shared\Request\RequestInterface;

final readonly class ResendVerificationCode implements RequestInterface
{
    public function __construct(
        public mixed $email,
        public mixed $type,
    ) {
    }

    public function toCommand(): Command
    {
        return new Command(
            $this->email,
            TokenType::from($this->type),
        );
    }
}
