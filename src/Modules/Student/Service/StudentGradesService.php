<?php

declare(strict_types=1);

namespace App\Modules\Student\Service;

use App\Modules\Grade\Facade\GradeFacade;
use App\Shared\Util\DateTimeFormatter;
use JetBrains\PhpStorm\ArrayShape;

final class StudentGradesService
{
    public function __construct(
        private GradeFacade $gradeFacade,
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
        $grades = $this->gradeFacade->findGradesByStudentIdAndSubjectId($studentId, $subjectId);

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
            'average' => $this->gradeFacade->countGradeAverage($data),
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
        $grades = $this->gradeFacade->findGradesForStudentWithSubjectInfo($studentId);

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
                'assignedBy' => sprintf('%s %s', $grade['teacherFirstName'], $grade['teacherLastName']),
            ];
        }

        foreach ($subjectsWithGrades as &$subject) {
            $average = $this->gradeFacade->countGradeAverage($subject['grades']);
            $subject['average'] = $average;
        }

        return $subjectsWithGrades;
    }
}
