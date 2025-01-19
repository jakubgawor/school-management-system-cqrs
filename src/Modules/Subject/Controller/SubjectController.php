<?php

declare(strict_types=1);

namespace App\Modules\Subject\Controller;

use App\Modules\Subject\Exception\AssignationNotFound;
use App\Modules\Subject\Exception\SubjectDoesNotExist;
use App\Modules\Subject\Exception\TeacherAlreadyAssignedSubject;
use App\Modules\Subject\Exception\TeacherDoesNotExist;
use App\Modules\Subject\Query\AllSubjectsListQuery;
use App\Modules\Subject\Request\V1\AssignClassRoomToSubject as AssignClassRoomToSubjectRequestV1;
use App\Modules\Subject\Request\V1\CreateSubject as CreateSubjectRequestV1;
use App\Modules\Subject\Request\V1\EditSubject as EditSubjectRequestV1;
use App\Modules\Subject\Request\V1\RemoveSubject as RemoveSubjectRequestV1;
use App\Modules\Subject\Request\V1\UnassignClassRoomFromSubject as UnassignClassRoomFromSubjectRequestV1;
use App\Shared\Command\Sync\CommandBus as SyncCommandBus;
use App\Shared\Request\Validator\RequestValidator;
use App\Shared\Request\Validator\ValidationError;
use OpenApi\Attributes\AdditionalProperties;
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

final class SubjectController extends AbstractController
{
    public function __construct(
        private RequestValidator $validator,
        private SyncCommandBus $syncCommandBus,
    ) {
    }

    #[Post(
        summary: 'Create subject',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ['teacherId', 'name'],
                properties: [
                    new Property(property: 'teacherId', type: 'string', format: 'uuid'),
                    new Property(property: 'name', type: 'string'),
                    new Property(property: 'description', type: 'string'),
                ],
                type: 'object',
            ),
        ),
        tags: ['Subject', 'v1'],
    )]
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/v1/subject/create', name: 'v1.subject.create', methods: ['POST'])]
    public function createSubject(CreateSubjectRequestV1 $request): Response
    {
        $this->validator->validate($request);

        try {
            $this->syncCommandBus->dispatch($request->toCommand());
        } catch (TeacherDoesNotExist|TeacherAlreadyAssignedSubject $exception) {
            throw new ValidationError([
                ValidationError::VALIDATION => [$exception->getValidationKey()],
            ]);
        }

        return new JsonResponse([
            'status' => 'ok',
        ], Response::HTTP_OK);
    }

    #[Post(
        summary: 'Assign class room to subject',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ['classRoomId'],
                properties: [
                    new Property(property: 'classRoomId', type: 'string', format: 'uuid'),
                ],
                type: 'object',
            ),
        ),
        tags: ['Subject', 'v1'],
    )]
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/v1/subject/{subjectId}/assign', name: 'v1.subject.assign', methods: ['POST'])]
    public function assignClassRoomToSubject(string $subjectId, AssignClassRoomToSubjectRequestV1 $request): Response
    {
        $request->subjectId = $subjectId;

        $this->validator->validate($request);

        try {
            $this->syncCommandBus->dispatch($request->toCommand());
        } catch (SubjectDoesNotExist $exception) {
            throw new ValidationError([
                ValidationError::VALIDATION => [$exception->getValidationKey()],
            ]);
        }

        return new JsonResponse([
            'status' => 'ok',
        ], Response::HTTP_OK);
    }

    #[Post(
        summary: 'Unassign class room from subject',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ['classRoomId'],
                properties: [
                    new Property(property: 'classRoomId', type: 'string', format: 'uuid'),
                ],
                type: 'object',
            ),
        ),
        tags: ['Subject', 'v1'],
    )]
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/v1/subject/{subjectId}/unassign', name: 'v1.subject.unassign', methods: ['POST'])]
    public function unassignClassRoomFromSubject(string $subjectId, UnassignClassRoomFromSubjectRequestV1 $request): Response
    {
        $request->subjectId = $subjectId;

        $this->validator->validate($request);

        try {
            $this->syncCommandBus->dispatch($request->toCommand());
        } catch (AssignationNotFound $exception) {
            throw new ValidationError([
                ValidationError::VALIDATION => [$exception->getValidationKey()],
            ]);
        }

        return new JsonResponse([
            'status' => 'ok',
        ], Response::HTTP_OK);
    }

    #[Get(
        summary: 'Get list of all subjects with class rooms and teacher info',
        tags: ['Subject', 'v1'],
        responses: [
            new OAResponse(
                response: 200,
                description: 'Get list of all subjects with class rooms and teacher info',
                content: new JsonContent(
                    type: 'object',
                    additionalProperties: new AdditionalProperties(
                        properties: [
                            new Property(property: 'id', type: 'string'),
                            new Property(property: 'name', type: 'string'),
                            new Property(property: 'description', type: 'string'),
                            new Property(
                                property: 'classRooms',
                                type: 'array',
                                items: new Items(
                                    properties: [
                                        new Property(property: 'id', type: 'string'),
                                        new Property(property: 'name', type: 'string'),
                                    ],
                                    type: 'object'
                                ),
                            ),
                            new Property(
                                property: 'teacher',
                                properties: [
                                    new Property(property: 'id', type: 'string'),
                                    new Property(property: 'firstName', type: 'string'),
                                    new Property(property: 'lastName', type: 'string'),
                                ],
                                type: 'object'
                            ),
                        ],
                        type: 'object'
                    ),
                ),
            ),
        ],
    )]
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/v1/subjects/list', name: 'v1.subjects.list', methods: ['GET'])]
    public function allSubjectsList(AllSubjectsListQuery $query): Response
    {
        return new JsonResponse($query->execute());
    }

    #[Patch(
        summary: 'Edit subject',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ['teacherId', 'name'],
                properties: [
                    new Property(property: 'teacherId', type: 'string', format: 'uuid'),
                    new Property(property: 'name', type: 'string'),
                    new Property(property: 'description', type: 'string'),
                ],
                type: 'object',
            ),
        ),
        tags: ['Subject', 'v1'],
        parameters: [
            new Parameter(
                name: 'id',
                description: 'Subject id',
                in: 'path',
                required: true,
                schema: new Schema(type: 'string'),
            ),
        ],
    )]
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/v1/subject/{subjectId}/edit', name: 'v1.subject.edit', methods: ['PATCH'])]
    public function editSubject(string $subjectId, EditSubjectRequestV1 $request): Response
    {
        $request->subjectId = $subjectId;

        $this->validator->validate($request);

        try {
            $this->syncCommandBus->dispatch($request->toCommand());
        } catch (SubjectDoesNotExist $exception) {
            throw new ValidationError([
                ValidationError::VALIDATION => [$exception->getValidationKey()],
            ]);
        }

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    #[Delete(
        summary: 'Remove subject',
        tags: ['Subject', 'v1'],
        parameters: [
            new Parameter(
                name: 'id',
                description: 'Subject id',
                in: 'path',
                required: true,
                schema: new Schema(type: 'string', format: 'uuid'),
            ),
        ],
    )]
    #[Route('/api/v1/subject/{subjectId}/remove', name: 'v1.subject.remove', methods: ['DELETE'])]
    public function removeSubject(string $subjectId, RemoveSubjectRequestV1 $request): Response
    {
        $request->subjectId = $subjectId;

        $this->validator->validate($request);

        try {
            $this->syncCommandBus->dispatch($request->toCommand());
        } catch (SubjectDoesNotExist $exception) {
            throw new ValidationError([
                ValidationError::VALIDATION => [$exception->getValidationKey()],
            ]);
        }

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
