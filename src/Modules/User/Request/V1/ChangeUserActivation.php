<?php

declare(strict_types=1);

namespace App\Modules\User\Request\V1;

use App\Modules\User\Command\Sync\ChangeUserActivation as Command;
use App\Shared\Request\RequestInterface;

final class ChangeUserActivation implements RequestInterface
{
    public function __construct(
        public mixed $userId,
        public readonly mixed $isActivated,
    ) {
    }

    public function toCommand(): Command
    {
        return new Command(
            $this->userId,
            $this->isActivated
        );
    }
}
