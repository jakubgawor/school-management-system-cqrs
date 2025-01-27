<?php

declare(strict_types=1);

namespace App\Modules\Teacher\Query;

use App\Modules\ClassRoom\Repository\ClassRoomRepository;
use App\Modules\Teacher\Repository\TeacherRepository;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class MyClassRoomsTeacherQuery
{
    public function __construct(
        private ClassRoomRepository $classRoomRepository,
        private TokenStorageInterface $tokenStorage,
        private TeacherRepository $teacherRepository,
    ) {
    }

    #[ArrayShape([
        'id' => 'string',
        'name' => 'string',
    ])]
    public function execute(): array
    {
        $teacherId = $this->teacherRepository->findByUserId($this->tokenStorage->getToken()->getUser()->getId());

        return $this->classRoomRepository->getClassRoomsByTeacherId($teacherId->getId());
    }
}
