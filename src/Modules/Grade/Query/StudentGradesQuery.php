<?php

declare(strict_types=1);

namespace App\Modules\Grade\Query;

use App\Modules\Grade\Service\StudentGradesService;
use Symfony\Component\HttpFoundation\RequestStack;

final class StudentGradesQuery
{
    public function __construct(
        private RequestStack $requestStack,
        private StudentGradesService $studentGradesService,
    ) {
    }

    public function execute(string $studentId): array
    {
        $subjectId = $this->requestStack->getCurrentRequest()->get('subjectId');

        if (! empty($subjectId)) {
            $data = $this->studentGradesService->handleStudentGradesWithSubjectId($studentId, $subjectId);
        } else {
            $data = $this->studentGradesService->handleStudentGrades($studentId);
        }

        return $data;
    }
}
