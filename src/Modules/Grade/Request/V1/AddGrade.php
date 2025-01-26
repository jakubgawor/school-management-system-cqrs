<?php

declare(strict_types=1);

namespace App\Modules\Grade\Request\V1;

use App\Modules\Grade\Command\Sync\AddGrade as Command;
use App\Modules\Grade\Enum\GradeValue;
use App\Shared\Request\RequestInterface;

final readonly class AddGrade implements RequestInterface
{
    public function __construct(
        public mixed $studentId,
        public mixed $subjectId,
        public mixed $grade,
        public mixed $weight,
        public mixed $description,
    ) {
    }

    public function toCommand(): Command
    {
        return new Command(
            $this->studentId,
            $this->subjectId,
            GradeValue::from($this->grade),
            $this->weight,
            $this->description,
        );
    }
}
