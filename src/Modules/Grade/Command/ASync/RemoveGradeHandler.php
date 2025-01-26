<?php

declare(strict_types=1);

namespace App\Modules\Grade\Command\ASync;

use App\Modules\Grade\Repository\GradeRepository;
use App\Shared\Command\Async\CommandHandler;

final class RemoveGradeHandler implements CommandHandler
{
    public function __construct(
        private GradeRepository $gradeRepository,
    ) {
    }

    public function __invoke(RemoveGrade $command): void
    {
        $grade = $this->gradeRepository->findGradeById($command->gradeId);
        $this->gradeRepository->remove($grade);
    }
}
