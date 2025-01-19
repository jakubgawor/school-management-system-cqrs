<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Query;

use App\Modules\ClassRoom\Entity\ClassRoom;
use App\Modules\ClassRoom\Query\DTO\ClassRoomList as ClassRoomListDTO;
use App\Modules\ClassRoom\Repository\ClassRoomRepository;
use Symfony\Component\HttpFoundation\RequestStack;

final class ClassRoomListQuery
{
    public function __construct(
        private RequestStack $requestStack,
        private ClassRoomRepository $classRoomRepository,
    ) {
    }

    public function execute(): array
    {
        $request = $this->requestStack->getCurrentRequest();

        $page = max(1, $request->query->getInt('page', 1));
        $limit = max(1, $request->query->getInt('limit', 10));

        if ($limit > 50) {
            $limit = 50;
        }

        $classRooms = $this->classRoomRepository->findPaginatedClassRooms($page, $limit);
        $totalCount = $this->classRoomRepository->countClassRooms();;

        $data = [];
        /** @var ClassRoom $classRoom */
        foreach ($classRooms as $classRoom) {
            $data[] = new ClassRoomListDTO(
                $classRoom->getId(),
                $classRoom->getName(),
                $classRoom->getCreatedAt(),
                $classRoom->getUpdatedAt(),
            );
        }

        return [
            'page' => $page,
            'limit' => $limit,
            'total' => $totalCount,
            'totalPages' => ceil($totalCount / $limit),
            'data' => $data,
        ];
    }
}
