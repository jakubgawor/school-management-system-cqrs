<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Request\V1;

use App\Modules\ClassRoom\Command\Sync\EditClassRoom as Command;
use App\Shared\Request\RequestInterface;

final class EditClassRoom implements RequestInterface
{
    public function __construct(
        public mixed $id,
        public readonly mixed $name,
    ) {
    }

    public function toCommand(): Command
    {
        return new Command(
            $this->id,
            $this->name,
        );
    }
}
