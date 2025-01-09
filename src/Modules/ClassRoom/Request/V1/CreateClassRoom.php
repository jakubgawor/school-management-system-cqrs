<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Request\V1;

use App\Modules\ClassRoom\Command\Sync\CreateClassRoom as Command;
use App\Shared\Request\RequestInterface;

final readonly class CreateClassRoom implements RequestInterface
{
    public function __construct(
        public mixed $name,
    ) {
    }

    public function toCommand(): Command
    {
        return new Command(
            $this->name,
        );
    }
}
