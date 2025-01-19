<?php

declare(strict_types=1);

namespace App\Modules\Teacher\Query;

use App\Modules\Teacher\Query\DTO\TeacherInfo;
use App\Modules\Teacher\Repository\TeacherRepository;
use Symfony\Component\HttpFoundation\RequestStack;

final class TeachersListQuery
{
    public function __construct(
        private TeacherRepository $teacherRepository,
        private RequestStack $requestStack,
    ) {
    }

    public function execute(): array
    {
        $request = $this->requestStack->getCurrentRequest();

        $searchPhrase = $request->get('searchPhrase');
        $page = max(1, $request->query->getInt('page', 1));
        $limit = max(1, $request->query->getInt('limit', 10));
        if ($limit > 50) {
            $limit = 50;
        }

        $teachers = $this->teacherRepository->findPaginatedTeachers($page, $limit, $searchPhrase);
        $totalCount = $this->teacherRepository->getCountOfPaginatedTeachers($searchPhrase);

        $data = [];
        foreach ($teachers as $teacher) {
            $data[] = new TeacherInfo(
                $teacher['id'],
                $teacher['userId'],
                $teacher['firstName'],
                $teacher['lastName'],
                $teacher['email'],
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
