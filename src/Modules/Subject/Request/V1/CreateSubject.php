<?php

declare(strict_types=1);

namespace App\Modules\Subject\Request\V1;

use App\Modules\Subject\Command\Sync\CreateSubject as Command;
use App\Shared\Request\RequestInterface;

final readonly class CreateSubject implements RequestInterface
{
    public function __construct(
        public mixed $teacherId,
        public mixed $name,
        public mixed $description,
    ) {
    }

    public function toCommand(): Command
    {
        return new Command(
            $this->teacherId,
            $this->name,
            $this->description
        );
    }
}
