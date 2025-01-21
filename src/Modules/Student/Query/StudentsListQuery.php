<?php

declare(strict_types=1);

namespace App\Modules\Student\Query;

use App\Modules\Student\Query\DTO\StudentInfo;
use App\Modules\Student\Repository\StudentRepository;
use Symfony\Component\HttpFoundation\RequestStack;

final class StudentsListQuery
{
    public function __construct(
        private StudentRepository $studentRepository,
        private RequestStack $requestStack,
    ) {
    }

    public function execute(): array
    {
        $request = $this->requestStack->getCurrentRequest();

        $fetchAll = (bool) $request->get('fetchAll', true);
        $search = $request->get('searchPhrase');
        $page = max(1, $request->query->getInt('page', 1));
        $limit = max(1, $request->query->getInt('limit', 10));
        if ($limit > 50) {
            $limit = 50;
        }

        $students = $this->studentRepository->findPaginatedStudents($page, $limit, $search, $fetchAll);

        $count = $this->studentRepository->getCountOfPaginatedStudents($search);

        $data = [];
        foreach ($students as $student) {
            $data[] = new StudentInfo(
                $student['id'],
                $student['userId'],
                $student['firstName'],
                $student['lastName'],
                $student['email'],
            );
        }

        return [
            'page' => $page,
            'limit' => $limit,
            'total' => $count,
            'totalPages' => ceil($count / $limit),
            'data' => $data,
        ];
    }
}
