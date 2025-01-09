<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Command\Sync;

use App\Shared\Command\Sync\Command;

final readonly class EditClassRoom implements Command
{
    public function __construct(
        public string $id,
        public string $name
    ) {
    }
}
