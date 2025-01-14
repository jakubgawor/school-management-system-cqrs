<?php

declare(strict_types=1);

namespace App\Modules\Subject\Command\Sync;

use App\Modules\Subject\Entity\Subject;
use App\Modules\Subject\Exception\TeacherAlreadyAssignedSubject;
use App\Modules\Subject\Exception\TeacherDoesNotExist;
use App\Modules\Subject\Repository\SubjectRepository;
use App\Modules\Teacher\Facade\TeacherFacade;
use App\Shared\Command\Sync\CommandHandler;
use App\Shared\Ramsey\IdGenerator;

class CreateSubjectHandler implements CommandHandler
{
    public function __construct(
        private SubjectRepository $subjectRepository,
        private TeacherFacade $teacherFacade,
    ) {
    }

    public function __invoke(CreateSubject $command): void
    {
        $teacher = $this->teacherFacade->existsTeacherById($command->teacherId);
        if (! $teacher) {
            throw new TeacherDoesNotExist();
        }

        if ($this->subjectRepository->countSubjectsByTeacherId($command->teacherId) >= 1) {
            throw new TeacherAlreadyAssignedSubject();
        }

        $subject = new Subject(
            IdGenerator::generate(),
            $command->teacherId,
            $command->name,
            $command->description,
        );

        $this->subjectRepository->save($subject);
    }
}
