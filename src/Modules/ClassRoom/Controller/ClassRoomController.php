<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Controller;

use App\Modules\ClassRoom\Exception\ClassRoomAlreadyExists;
use App\Modules\ClassRoom\Query\ClassRoomListQuery;
use App\Modules\ClassRoom\Request\V1\CreateClassRoom as CreateClassRoomRequestV1;
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
    #[Route('/api/v1/class_room/create', name: 'v1.class_room.create', methods: ['POST'])]
    public function createClassRoomV1(CreateClassRoomRequestV1 $request): JsonResponse
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

    #[Route('/api/v1/class_room/list', name: 'v1.class_room.list', methods: ['GET'])]
    public function classRoomList(ClassRoomListQuery $classRoomListQuery): JsonResponse
    {
        return new JsonResponse($classRoomListQuery->execute());
    }
}
