<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Controller;

use App\Modules\ClassRoom\Exception\ClassRoomAlreadyExists;
use App\Modules\ClassRoom\Exception\ClassRoomDoesNotExist;
use App\Modules\ClassRoom\Query\ClassRoomListQuery;
use App\Modules\ClassRoom\Query\StudentsListAssignedToClassRoom;
use App\Modules\ClassRoom\Request\V1\AddStudentToClassRoom as AddStudentToClassRoomRequestV1;
use App\Modules\ClassRoom\Request\V1\CreateClassRoom as CreateClassRoomRequestV1;
use App\Modules\ClassRoom\Request\V1\EditClassRoom as EditClassRoomRequestV1;
use App\Modules\ClassRoom\Request\V1\RemoveClassRoom as RemoveClassRoomRequestV1;
use App\Shared\Command\Sync\CommandBus as SyncCommandBus;
use App\Shared\Exception\BaseException;
use App\Shared\Request\Validator\RequestValidator;
use App\Shared\Request\Validator\ValidationError;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Patch;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response as OAResponse;
use OpenApi\Attributes\Schema;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ClassRoomController extends AbstractController
{
    public function __construct(
        private RequestValidator $validator,
        private SyncCommandBus $syncCommandBus,
    ) {
    }

    #[Post(
        summary: 'Create new class room',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ['name'],
                properties: [
                    new Property(property: 'name', description: 'Name of the class', type: 'string'),
                ],
                type: 'object'
            )
        ),
        tags: ['ClassRoom', 'v1']
    )]
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/v1/class_room/create', name: 'v1.class_room.create', methods: ['POST'])]
    public function createClassRoomV1(CreateClassRoomRequestV1 $request): Response
    {
        $this->validator->validate($request);

        try {
            $this->syncCommandBus->dispatch($request->toCommand());
        } catch (ClassRoomAlreadyExists $exception) {
            throw new ValidationError([
                ValidationError::VALIDATION => [$exception->getValidationKey()],
            ]);
        }

        return new JsonResponse([
            'status' => 'ok',
        ], Response::HTTP_CREATED);
    }

    #[Get(
        summary: 'Get list of all class rooms',
        tags: ['ClassRoom', 'v1'],
        parameters: [
            new Parameter(
                name: 'page',
                description: 'Page number',
                in: 'query',
                required: false,
                schema: new Schema(type: 'integer', example: 2)
            ),
            new Parameter(
                name: 'limit',
                description: 'Limit number of results',
                in: 'query',
                required: false,
                schema: new Schema(type: 'integer', example: 20)
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
                                    new Property(
                                        property: 'id',
                                        type: 'string',
                                        format: 'uuid',
                                        example: '01944cd2-235c-7245-85dc-79543815d1b7'
                                    ),
                                    new Property(
                                        property: 'name',
                                        type: 'string',
                                        example: '1h'
                                    ),
                                    new Property(
                                        property: 'createdAt',
                                        type: 'string',
                                        format: 'date-time',
                                        example: '2025-01-09T12:34:56Z'
                                    ),
                                ]
                            ),
                        ),
                    ]
                ),
            ),
        ],
    )]
    #[IsGranted('ROLE_TEACHER')]
    #[Route('/api/v1/class_room/list', name: 'v1.class_room.list', methods: ['GET'])]
    public function classRoomList(ClassRoomListQuery $classRoomListQuery): Response
    {
        return new JsonResponse($classRoomListQuery->execute());
    }

    #[Patch(
        summary: 'Edit class room',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ['name'],
                properties: [
                    new Property(property: 'name', description: 'Class room name', type: 'string'),
                ],
                type: 'object'
            )
        ),
        tags: ['ClassRoom', 'v1'],
        parameters: [
            new Parameter(
                name: 'id',
                description: 'Class room id',
                in: 'path',
                required: true,
                schema: new Schema(type: 'string', example: '01944ca2-9658-7828-8e73-058691d26a19')
            ),
        ],
    )]
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/v1/class_room/edit/{id}', name: 'v1.class_room.edit', methods: ['PATCH'])]
    public function editClassRoom(string $id, EditClassRoomRequestV1 $request): Response
    {
        $request->id = $id;

        $this->validator->validate($request);

        try {
            $this->syncCommandBus->dispatch($request->toCommand());
        } catch (ClassRoomDoesNotExist|ClassRoomAlreadyExists $exception) {
            throw new ValidationError([
                ValidationError::VALIDATION => [$exception->getValidationKey()],
            ]);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Delete(
        summary: 'Remove class room',
        tags: ['ClassRoom', 'v1'],
        parameters: [
            new Parameter(
                name: 'id',
                description: 'Class room id',
                in: 'path',
                required: true,
                schema: new Schema(type: 'string', example: '01944ca2-9658-7828-8e73-058691d26a19')
            ),
        ],
    )]
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/v1/class_room/remove/{id}', name: 'v1.class_room.remove', methods: ['DELETE'])]
    public function removeClassRoom(string $id): Response
    {
        try {
            $this->syncCommandBus->dispatch(new RemoveClassRoomRequestV1($id)->toCommand());
        } catch (ClassRoomDoesNotExist $exception) {
            throw new ValidationError([
                ValidationError::VALIDATION => [$exception->getValidationKey()],
            ]);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Post(
        summary: 'Add student to class room',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ['studentId'],
                properties: [
                    new Property(property: 'studentId', description: 'Student id', type: 'string'),
                ],
                type: 'object'
            )
        ),
        tags: ['ClassRoom', 'v1'],
        parameters: [
            new Parameter(
                name: 'id',
                description: 'Class room id',
                in: 'path',
                required: true,
                schema: new Schema(type: 'string', example: '01944ca2-9658-7828-8e73-058691d26a19')
            ),
        ],
    )]
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/v1/class_room/{id}/add_student', name: 'v1.class_room.add_student', methods: ['POST'])]
    public function addStudentToClassRoomV1(string $id, AddStudentToClassRoomRequestV1 $request): Response
    {
        $request->classRoomId = $id;

        $this->validator->validate($request);

        try {
            $this->syncCommandBus->dispatch($request->toCommand());
        } catch (BaseException $exception) {
            throw new ValidationError([
                ValidationError::VALIDATION => [$exception->getValidationKey()],
            ]);
        }

        return new JsonResponse([
            'status' => 'ok',
        ], Response::HTTP_OK);
    }

    #[Get(
        summary: 'Get list of students assigned to class room',
        tags: ['ClassRoom', 'v1'],
        parameters: [
            new Parameter(
                name: 'id',
                description: 'Class room id',
                in: 'path',
                required: true,
                schema: new Schema(type: 'string'),
            )
        ],
        responses: [
            new OAResponse(
                response: 200,
                description: 'Get list of students assigned to class room',
                content: new JsonContent(
                    type: 'array',
                    items: new Items(
                        properties: [
                            new Property(property: 'studentId', description: 'Student id'),
                            new Property(property: 'firstName', description: 'Student first name'),
                            new Property(property: 'lastName', description: 'Student last name'),
                            new Property(property: 'email', description: 'Student email'),
                        ],
                    ),
                ),
            ),
        ],
    )]
    #[Isgranted('ROLE_STUDENT')]
    #[Route('/api/v1/class_room/{id}/students', name: 'v1.class_room.students', methods: ['GET'])]
    public function studentsListAssignedToClassRoom(string $id, StudentsListAssignedToClassRoom $query): Response
    {
        return new JsonResponse($query->execute($id));
    }
}
