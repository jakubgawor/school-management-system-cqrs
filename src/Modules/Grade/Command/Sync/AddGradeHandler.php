<?php

declare(strict_types=1);

namespace App\Modules\Grade\Command\Sync;

use App\Modules\Grade\Entity\Grade;
use App\Modules\Grade\Exception\TeacherCanNotAssignGradeForSubject;
use App\Modules\Grade\Repository\GradeRepository;
use App\Modules\Student\Facade\StudentFacade;
use App\Modules\Subject\Facade\SubjectFacade;
use App\Modules\Teacher\Facade\TeacherFacade;
use App\Shared\Command\Sync\CommandHandler;
use App\Shared\Ramsey\IdGenerator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class AddGradeHandler implements CommandHandler
{
    public function __construct(
        private StudentFacade $studentFacade,
        private SubjectFacade $subjectFacade,
        private TeacherFacade $teacherFacade,
        private TokenStorageInterface $tokenStorage,
        private GradeRepository $gradeRepository,
    ) {
    }

    public function __invoke(AddGrade $command): void
    {
        $this->studentFacade->findStudentByIdOrFail($command->studentId);
        $this->subjectFacade->findSubjectByIdOrFail($command->subjectId);

        $teacher = $this->teacherFacade->findTeacherByUserId($this->tokenStorage->getToken()->getUser()->getId());

        if ($this->subjectFacade->findSubjectByIdOrFail($command->subjectId)->getTeacherId() !== $teacher->getId()) {
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
