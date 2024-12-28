<?php

declare(strict_types=1);

namespace App\Modules\User\Request\V1;

use App\Modules\User\Command\Sync\VerifyEmail as Command;
use App\Shared\Request\RequestInterface;

final readonly class VerifyEmail implements RequestInterface
{
    public function __construct(
        public mixed $token
    ) {
    }

    public function toCommand(): Command
    {
        return new Command(
            $this->token,
        );
    }
}
