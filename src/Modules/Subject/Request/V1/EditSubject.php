<?php

declare(strict_types=1);

namespace App\Modules\Subject\Request\V1;

use App\Modules\Subject\Command\Sync\EditSubject as Command;
use App\Shared\Request\RequestInterface;

final class EditSubject implements RequestInterface
{
    public function __construct(
        public mixed $subjectId,
        public readonly mixed $teacherId,
        public readonly mixed $name,
        public readonly mixed $description,
    ) {
    }

    public function toCommand(): Command
    {
        return new Command(
            $this->subjectId,
            $this->teacherId,
            $this->name,
            $this->description
        );
    }
}
