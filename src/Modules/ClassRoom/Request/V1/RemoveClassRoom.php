<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Request\V1;

use App\Modules\ClassRoom\Command\Sync\RemoveClassRoom as Command;
use App\Shared\Request\RequestInterface;

final class RemoveClassRoom implements RequestInterface
{
    public function __construct(
        public mixed $id,
    ) {
    }

    public function toCommand(): Command
    {
        return new Command(
            $this->id
        );
    }
}
