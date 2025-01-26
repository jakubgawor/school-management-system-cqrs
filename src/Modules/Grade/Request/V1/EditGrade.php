<?php

declare(strict_types=1);

namespace App\Modules\Grade\Request\V1;

use App\Modules\Grade\Command\ASync\EditGrade as Command;
use App\Modules\Grade\Enum\GradeValue;
use App\Shared\Request\RequestInterface;

final class EditGrade implements RequestInterface
{
    public function __construct(
        public mixed $gradeId,
        public readonly mixed $grade,
        public readonly mixed $weight,
        public readonly mixed $description,
    ) {
    }

    public function toCommand(): Command
    {
        return new Command(
            $this->gradeId,
            GradeValue::from($this->grade),
            $this->weight,
            $this->description,
        );
    }
}
