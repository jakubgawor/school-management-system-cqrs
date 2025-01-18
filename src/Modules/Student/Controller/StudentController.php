<?php

declare(strict_types=1);

namespace App\Modules\Student\Controller;

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
}
