<?php

declare(strict_types=1);

namespace App\Modules\Grade\Facade;

use App\Modules\Grade\Repository\GradeRepository;
use App\Modules\Grade\Service\GradeWeightedAverageService;

final class GradeFacade
{
    public function __construct(
        private GradeRepository $gradeRepository,
        private GradeWeightedAverageService $gradeWeightedAverageService,
    ) {
    }

    public function findGradesForStudentWithSubjectInfo(string $studentId): array
    {
        return $this->gradeRepository->findGradesForStudentWithSubjectInfo($studentId);
    }

    public function findGradesByStudentIdAndSubjectId(string $studentId, string $subjectId): array
    {
        return $this->gradeRepository->findGradesByStudentIdAndSubjectId($studentId, $subjectId);
    }

    public function countGradeAverage(array $grades): float
    {
        return $this->gradeWeightedAverageService->count($grades);
    }
}
