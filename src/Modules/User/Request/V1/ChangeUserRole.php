<?php

declare(strict_types=1);

namespace App\Modules\User\Request\V1;

use App\Modules\User\Command\Sync\ChangeUserRole as Command;
use App\Shared\Request\RequestInterface;

final class ChangeUserRole implements RequestInterface
{
    public function __construct(
        public mixed $id,
        public readonly mixed $role,
    ) {
    }

    public function toCommand(): Command
    {
        return new Command(
            $this->id,
            $this->role,
        );
    }
}
