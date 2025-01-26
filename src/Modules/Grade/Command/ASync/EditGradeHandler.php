<?php

declare(strict_types=1);

namespace App\Modules\Grade\Command\ASync;

use App\Modules\Grade\Repository\GradeRepository;
use App\Shared\Command\Async\CommandHandler;
use DateTimeImmutable;

final class EditGradeHandler implements CommandHandler
{
    public function __construct(
        private GradeRepository $gradeRepository,
    ) {
    }

    public function __invoke(EditGrade $command): void
    {
        $grade = $this->gradeRepository->findGradeById($command->gradeId);

        $grade
            ->setGrade($command->grade)
            ->setWeight($command->weight)
            ->setDescription($command->description)
            ->setUpdatedAt(new DateTimeImmutable());

        $this->gradeRepository->save($grade);
    }
}
