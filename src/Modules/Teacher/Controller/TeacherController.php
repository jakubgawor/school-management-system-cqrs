<?php

declare(strict_types=1);

namespace App\Modules\Teacher\Controller;

use App\Modules\Teacher\Query\MyClassRoomsTeacherQuery;
use App\Modules\Teacher\Query\MySubjectsTeacherQuery;
use App\Modules\Teacher\Query\TeachersListQuery;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Response as OAResponse;
use OpenApi\Attributes\Schema;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class TeacherController extends AbstractController
{
    #[Get(
        summary: 'Get list of teachers',
        tags: ['Teacher', 'v1'],
        parameters: [
            new Parameter(
                name: 'page',
                description: 'Page number',
                in: 'query',
                required: false,
                schema: new Schema(type: 'integer'),
            ),
            new Parameter(
                name: 'limit',
                description: 'Limit number of results',
                in: 'query',
                required: false,
                schema: new Schema(type: 'integer'),
            ),
            new Parameter(
                name: 'searchPhrase',
                description: 'Search phrase for user (first name, last name and email)',
                in: 'query',
                required: false,
                schema: new Schema(type: 'string'),
            ),
        ],
        responses: [
            new OAResponse(
                response: 200,
                description: 'Returns paginated list of all teachers',
                content: new JsonContent(
                    properties: [
                        new Property(property: 'page', type: 'integer', example: 2),
                        new Property(property: 'limit', type: 'integer', example: 7),
                        new Property(property: 'total', type: 'integer', example: 16),
                        new Property(property: 'totalPages', type: 'integer', example: 3),
                        new Property(
                            property: 'data',
                            type: 'array',
                            items: new Items(
                                properties: [
                                    new Property(property: 'teacherId', type: 'string', format: 'uuid'),
                                    new Property(property: 'userId', type: 'string', format: 'uuid'),
                                    new Property(property: 'firstName', type: 'string'),
                                    new Property(property: 'lastName', type: 'string'),
                                    new Property(property: 'email', type: 'string'),
                                ],
                                type: 'object'
                            ),
                        ),
                    ]
                ),
            ),
        ],
    )]
    #[IsGranted('ROLE_TEACHER')]
    #[Route('/api/v1/teachers/list', name: 'v1.teachers.list', methods: ['GET'])]
    public function teachersList(TeachersListQuery $query): Response
    {
        return new JsonResponse($query->execute());
    }

    #[Get(
        summary: 'Get list of subjects with class rooms for logged teacher',
        tags: ['Teacher', 'v1'],
        responses: [
            new OAResponse(
                response: 200,
                description: 'Get list of subjects with class rooms for logged teacher',
                content: new JsonContent(
                    properties: [
                        new Property(property: 'subjectId', type: 'string', format: 'uuid'),
                        new Property(property: 'teacherId', type: 'string', format: 'uuid'),
                        new Property(property: 'name', type: 'string'),
                        new Property(property: 'description', type: 'string'),
                        new Property(
                            property: 'classRooms',
                            type: 'array',
                            items: new Items(
                                properties: [
                                    new Property(property: 'teacherId', type: 'string', format: 'uuid'),
                                    new Property(property: 'name', type: 'string'),
                                ],
                                type: 'object'
                            ),
                        ),
                    ]
                ),
            ),
        ],
    )]
    #[IsGranted('EXACT_ROLE_TEACHER')]
    #[Route('/api/v1/teacher/my_subjects', name: 'v1.teachers.my_subjects', methods: ['GET'])]
    public function mySubjects(MySubjectsTeacherQuery $query): Response
    {
        return new JsonResponse($query->execute());
    }

    #[Get(
        summary: 'Get list of all class rooms you teach',
        tags: ['Teacher', 'v1'],
        responses: [
            new OAResponse(
                response: 200,
                description: 'Get list of all class rooms you teach',
                content: new JsonContent(
                    properties: [
                        new Property(property: 'id', type: 'string', format: 'uuid'),
                        new Property(property: 'name', type: 'string'),
                    ]
                ),
            ),
        ],
    )]
    #[IsGranted('EXACT_ROLE_TEACHER')]
    #[Route('/api/v1/teacher/my_class_rooms', name: 'v1.teachers.my_class_rooms', methods: ['GET'])]
    public function myClassRooms(MyClassRoomsTeacherQuery $query): Response
    {
        return new JsonResponse($query->execute());
    }
}
