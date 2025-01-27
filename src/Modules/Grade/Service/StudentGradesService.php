<?php

declare(strict_types=1);

namespace App\Modules\Grade\Service;

use App\Modules\Grade\Repository\GradeRepository;
use App\Shared\Util\DateTimeFormatter;
use JetBrains\PhpStorm\ArrayShape;

final class StudentGradesService
{
    public function __construct(
        private GradeRepository $gradeRepository,
        private GradeWeightedAverageService $gradeWeightedAverageService,
    ) {
    }

    #[ArrayShape([
        'average' => 'float',
        'grades' => [
            'id' => 'string',
            'grade' => 'float',
            'weight' => 'int',
            'description' => 'string',
            'createdAt' => 'string',
            'updatedAt' => 'string',
        ],
    ])]
    public function handleStudentGradesWithSubjectId(string $studentId, string $subjectId): array
    {
        $grades = $this->gradeRepository->findGradesByStudentIdAndSubjectId($studentId, $subjectId);

        $data = [];
        foreach ($grades as $grade) {
            $data[] = [
                'id' => $grade['id'],
                'grade' => $grade['grade']->value,
                'weight' => $grade['weight'],
                'description' => $grade['description'],
                'createdAt' => DateTimeFormatter::format($grade['createdAt']),
                'updatedAt' => DateTimeFormatter::format($grade['updatedAt']),
            ];
        }

        return [
            'average' => $this->gradeWeightedAverageService->count($data),
            'grades' => $data,
        ];
    }

    #[ArrayShape([
        'subjectId' => [
            'subjectId' => 'string',
            'subjectName' => 'string',
            'grades' => [
                'id' => 'string',
                'grade' => 'float',
                'weight' => 'int',
                'description' => 'string',
                'createdAt' => 'string',
                'updatedAt' => 'string',
            ],
        ],
    ])]
    public function handleStudentGrades(string $studentId): array
    {
        $grades = $this->gradeRepository->findGradesForStudentWithSubjectInfo($studentId);

        $subjectsWithGrades = [];
        foreach ($grades as $grade) {
            $subjectId = $grade['subjectId'];

            if (! isset($subjectsWithGrades[$subjectId])) {
                $subjectsWithGrades[$subjectId] = [
                    'subjectId' => $grade['subjectId'],
                    'subjectName' => $grade['subjectName'],
                    'grades' => [],
                ];
            }

            $subjectsWithGrades[$subjectId]['grades'][] = [
                'id' => $grade['gradeId'],
                'grade' => $grade['gradeValue'],
                'weight' => $grade['gradeWeight'],
                'description' => $grade['gradeDescription'],
                'createdAt' => $grade['gradeCreatedAt'],
                'updatedAt' => $grade['gradeUpdatedAt'],
            ];
        }

        foreach ($subjectsWithGrades as &$subject) {
            $average = $this->gradeWeightedAverageService->count($subject['grades']);
            $subject['average'] = $average;
        }

        return $subjectsWithGrades;
    }
}
