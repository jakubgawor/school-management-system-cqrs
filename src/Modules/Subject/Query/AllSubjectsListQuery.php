<?php

declare(strict_types=1);

namespace App\Modules\Subject\Query;

use App\Modules\Subject\Repository\SubjectRepository;

final class AllSubjectsListQuery
{
    public function __construct(
        private SubjectRepository $subjectRepository,
    ) {
    }

    public function execute(): array
    {
        $rows = $this->subjectRepository->getAllSubjectsWithClassRoomsData();

        $result = [];
        foreach ($rows as $row) {
            $subjectId = $row['subjectId'];

            if (! isset($result[$subjectId])) {
                $result[$subjectId] = [
                    'id' => $subjectId,
                    'name' => $row['subjectName'],
                    'description' => $row['subjectDescription'],
                    'classRooms' => [],
                ];
            }

            $result[$subjectId]['classRooms'][] = [
                'id' => $row['classRoomId'],
                'name' => $row['classRoomName'],
            ];

            $result[$subjectId]['teacher'] = [
                'id' => $row['teacherId'],
                'firstName' => $row['teacherFirstName'],
                'lastName' => $row['teacherLastName'],
            ];
        }

        return $result;
    }
}
