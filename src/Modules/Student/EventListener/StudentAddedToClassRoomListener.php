<?php

declare(strict_types=1);

namespace App\Modules\Student\EventListener;

use App\Modules\ClassRoom\Event\StudentAddedToClassRoom;
use App\Modules\Student\Exception\StudentDoesNotExist;
use App\Modules\Student\Repository\StudentRepository;

final class StudentAddedToClassRoomListener
{
    public function __construct(
        private StudentRepository $studentRepository,
    ) {
    }

    public function onStudentAddedToClassRoom(StudentAddedToClassRoom $event): void
    {
        $student = $this->studentRepository->findStudentById($event->studentId);
        if (! $student) {
            throw new StudentDoesNotExist();
        }

        $student->setClassRoomId($event->classRoomId);
        $this->studentRepository->save($student);
    }
}
