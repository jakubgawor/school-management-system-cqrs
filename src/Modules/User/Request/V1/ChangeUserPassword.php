<?php

declare(strict_types=1);

namespace App\Modules\User\Request\V1;

use App\Modules\User\Command\Sync\ChangeUserPassword as Command;
use App\Shared\Request\RequestInterface;

final readonly class ChangeUserPassword implements RequestInterface
{
    public function __construct(
        public mixed $currentPassword,
        public mixed $newPassword,
        public mixed $newPasswordConfirmation,
    ) {
    }

    public function toCommand(): Command
    {
        return new Command(
            $this->currentPassword,
            $this->newPassword,
            $this->newPasswordConfirmation,
        );
    }
}
