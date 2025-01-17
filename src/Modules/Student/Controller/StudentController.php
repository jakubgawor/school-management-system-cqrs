<?php

declare(strict_types=1);

namespace App\Modules\Student\Controller;

use App\Modules\Student\Request\V1\RemoveStudentClassRoom as RemoveStudentClassRoomRequestV1;
use App\Shared\Command\Sync\CommandBus as SyncCommandBus;
use App\Shared\Exception\BaseException;
use App\Shared\Request\Validator\RequestValidator;
use App\Shared\Request\Validator\ValidationError;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Schema;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StudentController extends AbstractController
{
    public function __construct(
        private RequestValidator $validator,
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
}
