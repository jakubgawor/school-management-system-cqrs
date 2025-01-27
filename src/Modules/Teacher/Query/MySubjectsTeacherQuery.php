<?php

declare(strict_types=1);

namespace App\Modules\Teacher\Query;

use App\Modules\Subject\Repository\SubjectRepository;
use App\Modules\Teacher\Repository\TeacherRepository;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class MySubjectsTeacherQuery
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private TeacherRepository $teacherRepository,
        private SubjectRepository $subjectRepository,
    ) {
    }

    #[ArrayShape([
        'id' => 'string',
        'teacherId' => 'strinig',
        'name' => 'string',
        'description' => 'string',
        'classRooms' => [
            'id' => 'string',
            'name' => 'string',
        ],
    ])]
    public function execute(): array
    {
        $teacher = $this->teacherRepository->findByUserId($this->tokenStorage->getToken()->getUser()->getId());

        $subjects = $this->subjectRepository->getSubjectsWithClassRoomsByTeacherId($teacher->getId());

        $subjectsPrettyPrint = [];
        foreach ($subjects as $subject) {
            $subjectId = $subject['subjectId'];

            if (! isset($subjectsPrettyPrint[$subjectId])) {
                $subjectsPrettyPrint[$subjectId] = [
                    'subjectId' => $subject['subjectId'],
                    'teacherId' => $subject['teacherId'],
                    'name' => $subject['subjectName'],
                    'description' => $subject['subjectDescription'],
                    'classRooms' => [],
                ];
            }

            if ($subject['classRoomId'] !== null) {
                $subjectsPrettyPrint[$subjectId]['classRooms'][] = [
                    'id' => $subject['classRoomId'],
                    'name' => $subject['classRoomName'],
                ];
            }
        }

        return $subjectsPrettyPrint;
    }
}
