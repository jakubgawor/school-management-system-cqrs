<?php

declare(strict_types=1);

namespace App\Modules\Subject\Request\V1;

use App\Modules\Subject\Command\Sync\RemoveSubject as Command;
use App\Shared\Request\RequestInterface;

final class RemoveSubject implements RequestInterface
{
    public function __construct(
        public mixed $subjectId,
    ) {
    }

    public function toCommand(): Command
    {
        return new Command(
            $this->subjectId,
        );
    }
}
