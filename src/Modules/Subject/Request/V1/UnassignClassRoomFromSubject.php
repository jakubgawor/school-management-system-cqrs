<?php

declare(strict_types=1);

namespace App\Modules\Subject\Request\V1;

use App\Modules\Subject\Command\Sync\UnassignClassRoomFromSubject as Command;
use App\Shared\Request\RequestInterface;

final class UnassignClassRoomFromSubject implements RequestInterface
{
    public function __construct(
        public mixed $subjectId,
        public readonly mixed $classRoomId,
    ) {
    }

    public function toCommand(): Command
    {
        return new Command(
            $this->subjectId,
            $this->classRoomId,
        );
    }
}
