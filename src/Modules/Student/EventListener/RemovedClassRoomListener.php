<?php

declare(strict_types=1);

namespace App\Modules\Student\EventListener;

use App\Modules\ClassRoom\Event\ClassRoomRemoved;
use App\Modules\Student\Entity\Student;
use App\Modules\Student\Repository\StudentRepository;

final class RemovedClassRoomListener
{
    public function __construct(
        private StudentRepository $studentRepository,
    ) {
    }

    public function onClassRoomRemoved(ClassRoomRemoved $event): void
    {
        $students = $this->studentRepository->findStudentAssignedToClassRoom($event->classRoomId);

        /** @var Student $student */
        foreach ($students as $student) {
            $student->setClassRoomId(null);
            $this->studentRepository->save($student);
        }
    }
}
