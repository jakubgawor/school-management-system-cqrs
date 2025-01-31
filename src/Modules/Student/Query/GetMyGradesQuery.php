<?php

declare(strict_types=1);

namespace App\Modules\Student\Query;

use App\Modules\Student\Repository\StudentRepository;
use App\Modules\Student\Service\StudentGradesService;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class GetMyGradesQuery
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private StudentRepository $studentRepository,
        private StudentGradesService $studentGradesService,
    ) {
    }

    public function execute(): array
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $studentId = $this->studentRepository->findByUserId($user->getId())->getId();

        return $this->studentGradesService->handleStudentGrades($studentId);
    }
}
