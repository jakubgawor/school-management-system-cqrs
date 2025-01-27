<?php

declare(strict_types=1);

namespace App\Modules\Grade\Query;

use App\Modules\Grade\Query\DTO\StudentGrades;
use App\Modules\Grade\Repository\GradeRepository;
use App\Modules\Grade\Service\GradeWeightedAverageService;
use App\Shared\Util\DateTimeFormatter;
use Symfony\Component\HttpFoundation\RequestStack;

final class StudentGradesQuery
{
    public function __construct(
        private GradeRepository $gradeRepository,
        private RequestStack $requestStack,
        private GradeWeightedAverageService $gradeWeightedAverageService,
    ) {
    }

    public function execute(string $studentId): array
    {
        $subjectId = $this->requestStack->getCurrentRequest()->get('subjectId');

        $grades = $this->gradeRepository->findGradesByStudentIdAndSubjectId($studentId, $subjectId);

        $data = [];
        foreach ($grades as $grade) {
            $data[] = new StudentGrades(
                $grade['id'],
                $grade['grade']->value,
                $grade['weight'],
                $grade['description'],
                DateTimeFormatter::format($grade['createdAt']),
                DateTimeFormatter::format($grade['updatedAt']),
            );
        }

        return [
            'average' => $this->gradeWeightedAverageService->count($grades),
            'grades' => $data,
        ];
    }
}
