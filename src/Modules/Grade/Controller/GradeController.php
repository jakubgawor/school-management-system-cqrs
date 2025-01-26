<?php

declare(strict_types=1);

namespace App\Modules\Grade\Controller;

use App\Modules\Grade\Command\ASync\RemoveGrade;
use App\Modules\Grade\Request\V1\AddGrade as AddGradeRequestV1;
use App\Modules\Grade\Request\V1\EditGrade as EditGradeRequestV1;
use App\Shared\Command\Async\CommandBus as AsyncCommandBus;
use App\Shared\Command\Sync\CommandBus as SyncCommandBus;
use App\Shared\Exception\BaseException;
use App\Shared\Request\Validator\RequestValidator;
use App\Shared\Request\Validator\ValidationError;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Patch;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Schema;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class GradeController extends AbstractController
{
    public function __construct(
        private RequestValidator $validator,
        private SyncCommandBus $syncCommandBus,
        private AsyncCommandBus $asyncCommandBus,
        private readonly RequestValidator $requestValidator,
    ) {
    }

    #[Post(
        summary: 'Add new grade',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ['studentId', 'subjectId', 'grade', 'weight', 'description'],
                properties: [
                    new Property(property: 'studentId', description: 'Student id', type: 'string', format: 'uuid'),
                    new Property(property: 'subjectId', description: 'Subject id', type: 'string', format: 'uuid'),
                    new Property(property: 'grade', description: 'Grade value', type: 'string'),
                    new Property(property: 'weight', description: 'Grade weight', type: 'integer'),
                    new Property(property: 'description', description: 'Grade description', type: 'string'),
                ],
                type: 'object'
            )
        ),
        tags: ['Grade', 'v1']
    )]
    #[IsGranted('ADD_GRADE')]
    #[Route('/api/v1/grade/add', name: 'v1.grade.add', methods: ['POST'])]
    public function addGrade(AddGradeRequestV1 $request): Response
    {
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
        ], Response::HTTP_CREATED);
    }

    #[Delete(
        summary: 'Remove grade',
        tags: ['Grade', 'v1'],
        parameters: [
            new Parameter(
                name: 'Grade id',
                description: 'Grade id',
                in: 'path',
                required: true,
                schema: new Schema(type: 'string', format: 'uuid')
            ),
        ],
    )]
    #[IsGranted('ROLE_TEACHER')]
    #[Route('/api/v1/grade/{gradeId}/remove', name: 'v1.grade.remove', methods: ['DELETE'])]
    public function deleteGrade(string $gradeId): Response
    {
        $this->asyncCommandBus->dispatch(new RemoveGrade($gradeId));

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Patch(
        summary: 'Edit grade',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ['grade', 'weight', 'description'],
                properties: [
                    new Property(property: 'grade', description: 'Grade value', type: 'string'),
                    new Property(property: 'weight', description: 'Grade weight', type: 'integer'),
                    new Property(property: 'description', description: 'Grade description', type: 'string'),
                ],
                type: 'object',
            ),
        ),
        tags: ['Grade', 'v1'],
        parameters: [
            new Parameter(
                name: 'gradeId',
                description: 'Grade ID',
                in: 'path',
                required: true,
            ),
        ],
    )]
    #[IsGranted('ROLE_TEACHER')]
    #[Route('/api/v1/grade/{gradeId}/edit', name: 'v1.grade.edit', methods: ['PATCH'])]
    public function editGrade(string $gradeId, EditGradeRequestV1 $request): Response
    {
        $request->gradeId = $gradeId;
        $this->requestValidator->validate($request);

        $this->asyncCommandBus->dispatch($request->toCommand());

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
