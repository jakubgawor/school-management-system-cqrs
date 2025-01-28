<?php

declare(strict_types=1);

namespace App\Modules\Student\Request\V1;

use App\Modules\Student\Command\Sync\RemoveStudentClassRoom as Command;
use App\Shared\Request\RequestInterface;

final class RemoveStudentClassRoom implements RequestInterface
{
    public function __construct(
        public mixed $id,
    ) {
    }

    public function toCommand(): Command
    {
        return new Command(
            $this->id,
        );
    }
}
