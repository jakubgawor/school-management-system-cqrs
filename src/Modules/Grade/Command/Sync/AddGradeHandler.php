<?php

declare(strict_types=1);

namespace App\Modules\Grade\Command\Sync;

use App\Modules\Grade\Entity\Grade;
use App\Modules\Grade\Exception\TeacherCanNotAssignGradeForSubject;
use App\Modules\Grade\Repository\GradeRepository;
use App\Modules\Student\Exception\StudentDoesNotExist;
use App\Modules\Student\Repository\StudentRepository;
use App\Modules\Subject\Exception\SubjectDoesNotExist;
use App\Modules\Subject\Repository\SubjectRepository;
use App\Modules\Teacher\Repository\TeacherRepository;
use App\Shared\Command\Sync\CommandHandler;
use App\Shared\Ramsey\IdGenerator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class AddGradeHandler implements CommandHandler
{
    public function __construct(
        private StudentRepository $studentRepository,
        private SubjectRepository $subjectRepository,
        private TokenStorageInterface $tokenStorage,
        private TeacherRepository $teacherRepository,
        private GradeRepository $gradeRepository,
    ) {
    }

    public function __invoke(AddGrade $command): void
    {
        if (! $this->studentRepository->findStudentById($command->studentId)) {
            throw new StudentDoesNotExist();
        }

        if (! $this->subjectRepository->findSubjectById($command->subjectId)) {
            throw new SubjectDoesNotExist();
        }

        $teacher = $this->teacherRepository->findByUserId($this->tokenStorage->getToken()->getUser()->getId());

        if ($this->subjectRepository->findSubjectById($command->subjectId)->getTeacherId() !== $teacher->getId()) {
            throw new TeacherCanNotAssignGradeForSubject();
        }

        $grade = new Grade(
            IdGenerator::generate(),
            $teacher->getId(),
            $command->studentId,
            $command->subjectId,
            $command->grade,
            $command->weight,
            $command->description,
        );

        $this->gradeRepository->save($grade);
    }
}
