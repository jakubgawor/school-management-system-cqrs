<?php

declare(strict_types=1);

namespace App\Modules\Student\Controller;

use App\Modules\Student\Query\GetMyGradesQuery;
use App\Modules\Student\Query\StudentDetailsQuery;
use App\Modules\Student\Query\StudentGradesQuery;
use App\Modules\Student\Query\StudentsListQuery;
use App\Modules\Student\Request\V1\RemoveStudentClassRoom as RemoveStudentClassRoomRequestV1;
use App\Shared\Command\Sync\CommandBus as SyncCommandBus;
use App\Shared\Exception\BaseException;
use App\Shared\Request\Validator\ValidationError;
use OpenApi\Attributes\Delete;
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

final class StudentController extends AbstractController
{
    public function __construct(
        private SyncCommandBus $syncCommandBus,
    ) {
    }

    #[Delete(
        summary: 'Remove student from class',
        tags: ['Student', 'v1'],
        parameters: [
            new Parameter(
                name: 'id',
                description: 'Student id',
                in: 'path',
                required: true,
                schema: new Schema(type: 'string', example: '01944ca2-9658-7828-8e73-058691d26a19')
            ),
        ],
    )]
    #[Route('/api/v1/student/{id}/remove_class_room', name: 'v1.student.remove_student_class_room', methods: ['DELETE'])]
    public function removeStudentClassRoom(string $id): Response
    {
        try {
            $this->syncCommandBus->dispatch(new RemoveStudentClassRoomRequestV1($id)->toCommand());
        } catch (BaseException $exception) {
            throw new ValidationError([
                ValidationError::VALIDATION => [$exception->getValidationKey()],
            ]);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Get(
        summary: 'Get list of students',
        tags: ['Student', 'v1'],
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
            new Parameter(
                name: 'fetchAll',
                description: 'Fetch all students or fetch students without class',
                in: 'query',
                required: false,
                schema: new Schema(type: 'bool'),
            ),
        ],
        responses: [
            new OAResponse(
                response: 200,
                description: 'Returns paginated list of all class rooms',
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
                                    new Property(property: 'id', type: 'string', format: 'uuid'),
                                    new Property(property: 'userId', type: 'string', format: 'uuid'),
                                    new Property(property: 'firstName', type: 'string'),
                                    new Property(property: 'lastName', type: 'string'),
                                    new Property(property: 'email', type: 'string'),
                                ]
                            ),
                        ),
                    ]
                ),
            ),
        ],
    )]
    #[IsGranted('ROLE_TEACHER')]
    #[Route('/api/v1/students/list', name: 'v1.student.list', methods: ['GET'])]
    public function studentsList(StudentsListQuery $query): Response
    {
        return new JsonResponse($query->execute());
    }

    #[Get(
        summary: 'Get student details with subject info',
        tags: ['Student', 'v1'],
        parameters: [
            new Parameter(
                name: 'studentId',
                description: 'Student id',
                in: 'path',
                required: false,
                schema: new Schema(type: 'string', format: 'uuid'),
            ),
        ],
        responses: [
            new OAResponse(
                response: 200,
                description: 'Returns student details with subject info',
                content: new JsonContent(
                    properties: [
                        new Property(property: 'studentFirstName', type: 'string'),
                        new Property(property: 'studentLastName', type: 'string'),
                        new Property(property: 'studentClassRoomId', type: 'string', format: 'uuid'),
                        new Property(
                            property: 'subjects',
                            type: 'array',
                            items: new Items(
                                properties: [
                                    new Property(property: 'id', type: 'string', format: 'uuid'),
                                    new Property(property: 'name', type: 'string'),
                                    new Property(property: 'teacherFirstName', type: 'string'),
                                    new Property(property: 'teacherLastName', type: 'string'),
                                    new Property(property: 'teacherEmail', type: 'string'),
                                ]
                            ),
                        ),
                    ],
                ),
            ),
        ]
    )]
    #[IsGranted('ROLE_TEACHER')]
    #[Route('/api/v1/student/{studentId}/details', name: 'v1.student.details', methods: ['GET'])]
    public function getStudentDetails(string $studentId, StudentDetailsQuery $query): Response
    {
        return new JsonResponse($query->execute($studentId));
    }

    #[Get(
        summary: 'Get student grades',
        tags: ['Student', 'v1'],
        parameters: [
            new Parameter(
                name: 'studentId',
                description: 'Student id',
                in: 'path',
                required: false,
                schema: new Schema(type: 'string', format: 'uuid'),
            ),
            new Parameter(
                name: 'subjectId',
                description: 'Subject id',
                in: 'query',
                required: false,
                schema: new Schema(type: 'string', format: 'uuid'),
            ),
        ],
        responses: [
            new OAResponse(
                response: 200,
                description: 'Returns student grades list',
                content: new JsonContent(
                    properties: [
                        new Property(property: 'average', type: 'float'),
                        new Property(
                            property: 'grades',
                            type: 'array',
                            items: new Items(
                                properties: [
                                    new Property(property: 'id', type: 'string', format: 'uuid'),
                                    new Property(property: 'grade', type: 'string'),
                                    new Property(property: 'weight', type: 'integer'),
                                    new Property(property: 'description', type: 'string'),
                                    new Property(property: 'createdAt', type: 'string'),
                                    new Property(property: 'updatedAt', type: 'string'),
                                ]
                            ),
                        ),
                    ],
                ),
            ),
        ]
    )]
    #[IsGranted('ROLE_TEACHER')]
    #[Route('/api/v1/student/{studentId}/grades', name: 'v1.student.grades', methods: ['GET'])]
    public function getStudentGrades(string $studentId, StudentGradesQuery $query): Response
    {
        return new JsonResponse($query->execute($studentId));
    }

    #[Get(
        summary: 'Returns current logged student grades list with subject info',
        tags: ['Student', 'v1'],
        responses: [
            new OAResponse(
                response: 200,
                description: 'Returns current logged student grades list with subject info',
                content: new JsonContent(
                    properties: [
                        new Property(property: 'average', type: 'float'),
                        new Property(
                            property: 'grades',
                            type: 'array',
                            items: new Items(
                                properties: [
                                    new Property(property: 'id', type: 'string', format: 'uuid'),
                                    new Property(property: 'grade', type: 'string'),
                                    new Property(property: 'weight', type: 'integer'),
                                    new Property(property: 'description', type: 'string'),
                                    new Property(property: 'createdAt', type: 'string'),
                                    new Property(property: 'updatedAt', type: 'string'),
                                    new Property(property: 'teacherFirstName', type: 'string'),
                                    new Property(property: 'teacherLastName', type: 'string'),
                                ]
                            ),
                        ),
                    ],
                ),
            ),
        ]
    )]
    #[IsGranted('EXACT_ROLE_STUDENT')]
    #[Route('/api/v1/student/my_grades', name: 'v1.student.my_grades', methods: ['GET'])]
    public function getMyGrades(GetMyGradesQuery $query): Response
    {
        return new JsonResponse($query->execute());
    }
}
