<?php

declare(strict_types=1);

namespace App\Modules\Subject\Controller;

use App\Modules\Subject\Exception\SubjectDoesNotExist;
use App\Modules\Subject\Exception\TeacherAlreadyAssignedSubject;
use App\Modules\Subject\Exception\TeacherDoesNotExist;
use App\Modules\Subject\Request\V1\AssignClassRoomToSubject as AssignClassRoomToSubjectRequestV1;
use App\Modules\Subject\Request\V1\CreateSubject as CreateSubjectRequestV1;
use App\Shared\Command\Sync\CommandBus as SyncCommandBus;
use App\Shared\Request\Validator\RequestValidator;
use App\Shared\Request\Validator\ValidationError;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\RequestBody;
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
}
