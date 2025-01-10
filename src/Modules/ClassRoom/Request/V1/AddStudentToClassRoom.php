<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Request\V1;

use App\Modules\ClassRoom\Command\Sync\AddStudentToClassRoom as Command;
use App\Shared\Request\RequestInterface;

final class AddStudentToClassRoom implements RequestInterface
{
    public function __construct(
        public mixed $classRoomId,
        public readonly mixed $studentId,
    ) {
    }

    public function toCommand(): Command
    {
        return new Command(
            $this->classRoomId,
            $this->studentId,
        );
    }
}
