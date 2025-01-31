<?php

declare(strict_types=1);

namespace App\Modules\Student\Query;

use App\Modules\Student\Repository\StudentRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class GetMySubjectsQuery
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private StudentRepository $studentRepository,
    ) {
    }

    public function execute(): array
    {
        $currentUserId = $this->tokenStorage->getToken()->getUser()->getId();
        $student = $this->studentRepository->findByUserId($currentUserId);

        return $this->studentRepository->getStudentDetailsWithSubjectsAndTeacher($student->getId());
    }
}
