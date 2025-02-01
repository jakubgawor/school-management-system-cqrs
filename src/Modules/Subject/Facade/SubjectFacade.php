<?php

declare(strict_types=1);

namespace App\Modules\Subject\Facade;

use App\Modules\Subject\Entity\Subject;
use App\Modules\Subject\Exception\SubjectDoesNotExist;
use App\Modules\Subject\Repository\SubjectRepository;

final class SubjectFacade
{
    public function __construct(
        private SubjectRepository $subjectRepository,
    ) {
    }

    public function findSubjectByIdOrFail(string $subjectId): Subject
    {
        $subject = $this->subjectRepository->findSubjectById($subjectId);

        if (! $subject) {
            throw new SubjectDoesNotExist();
        }

        return $subject;
    }

    public function isTeacherAssignedToSubject(string $teacherId): bool
    {
        return $this->subjectRepository->countSubjectsByTeacherId($teacherId) !== 0;
    }
}
