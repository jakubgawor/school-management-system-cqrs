<?php

declare(strict_types=1);

namespace App\Modules\User\Request\V1;

use App\Modules\User\Command\Sync\ChangeUserEmail as Command;
use App\Shared\Request\RequestInterface;

final readonly class ChangeUserEmail implements RequestInterface
{
    public function __construct(
        public mixed $email,
    ) {
    }

    public function toCommand(): Command
    {
        return new Command(
            $this->email,
        );
    }
}
