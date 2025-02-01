<?php

declare(strict_types=1);

namespace App\Modules\User\Security\Validator;

use App\Modules\Student\Facade\StudentFacade;
use App\Modules\Subject\Facade\SubjectFacade;
use App\Modules\Teacher\Facade\TeacherFacade;
use App\Modules\User\Entity\User;
use App\Modules\User\Enum\Role;
use App\Modules\User\Exception\StudentIsAssignedToClassRoom;
use App\Modules\User\Exception\TeacherIsAssignedToSubject;

final class UserRoleValidator
{
    public function __construct(
        private TeacherFacade $teacherFacade,
        private SubjectFacade $subjectFacade,
        private StudentFacade $studentFacade,
    ) {
    }

    public function validateTeacherRole(User $user): void
    {
        if ($user->getRoles()[0] === Role::TEACHER->value) {
            $teacher = $this->teacherFacade->findTeacherByUserId($user->getId());
            if ($this->subjectFacade->isTeacherAssignedToSubject($teacher->getId())) {
                throw new TeacherIsAssignedToSubject();
            }
        }
    }

    public function validateStudentRole(User $user): void
    {
        if ($user->getRoles()[0] === Role::STUDENT->value) {
            $student = $this->studentFacade->findStudentByUserId($user->getId());
            if (! empty($student->getClassRoomId())) {
                throw new StudentIsAssignedToClassRoom();
            }
        }
    }
}
