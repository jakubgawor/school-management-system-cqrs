<?php

declare(strict_types=1);

namespace App\Modules\Announcement\Controller;

use App\Modules\Announcement\Command\Sync\RemoveAnnouncement;
use App\Modules\Announcement\Request\V1\CreateNewAnnouncement as CreateNewAnnouncementRequestV1;
use App\Modules\Announcement\Request\V1\EditAnnouncement as EditAnnouncementRequestV1;
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

final class AnnouncementController extends AbstractController
{
    public function __construct(
        private RequestValidator $validator,
        private SyncCommandBus $syncCommandBus,
    ) {
    }

    #[Post(
        summary: 'Create new announcement',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ['title', 'message'],
                properties: [
                    new Property(property: 'title', description: 'First name', type: 'string'),
                    new Property(property: 'message', description: 'Last name', type: 'string'),
                ],
                type: 'object'
            )
        ),
        tags: ['Announcement', 'v1']
    )]
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/v1/announcement/create', name: 'v1.announcement.create', methods: ['POST'])]
    public function createNewAnnouncement(CreateNewAnnouncementRequestV1 $request): Response
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

    #[Patch(
        summary: 'Edit announcement',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ['title', 'message'],
                properties: [
                    new Property(property: 'title', description: 'First name', type: 'string'),
                    new Property(property: 'message', description: 'Last name', type: 'string'),
                ],
                type: 'object'
            )
        ),
        tags: ['Announcement', 'v1'],
        parameters: [
            new Parameter(
                name: 'announcementId',
                description: 'Announcement ID',
                in: 'path',
                required: true,
            ),
        ],
    )]
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/v1/announcement/{announcementId}/edit', name: 'v1.announcement.edit', methods: ['PATCH'])]
    public function editAnnouncement(string $announcementId, EditAnnouncementRequestV1 $request): Response
    {
        $request->id = $announcementId;

        $this->validator->validate($request);

        try {
            $this->syncCommandBus->dispatch($request->toCommand());
        } catch (BaseException $exception) {
            throw new ValidationError([
                ValidationError::VALIDATION => [$exception->getValidationKey()],
            ]);
        }

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    #[Delete(
        summary: 'Remove announcement',
        tags: ['Announcement', 'v1'],
        parameters: [
            new Parameter(
                name: 'announcementId',
                description: 'Announcement id',
                in: 'path',
                required: true,
                schema: new Schema(type: 'string')
            ),
        ],
    )]
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/v1/announcement/{announcementId}/remove', name: 'v1.announcement.remove', methods: ['DELETE'])]
    public function removeAnnouncement(string $announcementId): Response
    {
        try {
            $this->syncCommandBus->dispatch(new RemoveAnnouncement($announcementId));
        } catch (BaseException $exception) {
            throw new ValidationError([
                ValidationError::VALIDATION => [$exception->getValidationKey()],
            ]);
        }

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
