<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class SecurityService extends AbstractController
{
    private int $clientId;
    private int $pathId;

    public function areClientIdsMatching(): bool
    {
        return $this->clientId === $this->pathId;
    }

    public function jsonToReturnIfBadRequest(): JsonResponse
    {
        return $this->json(
            [
                'status' => 400,
                'message' => 'Bad request. There are errors on given fields.'
            ],
            400
        );
    }

    public function jsonToReturnIfNotFound(): JsonResponse
    {
        return $this->json(
            [
                'status' => 404,
                'message' => 'Resource not found.'
            ],
            404
        );
    }

    public function jsonToReturnIfUserCreated(int $clientId, int $userId): JsonResponse
    {
        return $this->json(
            [
                'status' => 201,
                'message' => 'Resource created successfully.',
                'uri' => '/clients/' . $clientId . '/users/' . $userId
            ],
            201
        );
    }

    public function jsonToReturnIfResourceDeleted(): JsonResponse
    {
        return $this->json(
            [
                'status' => 200,
                'message' => 'Resource deleted successfully.'
            ],
            200
        );
    }

    /**
     * @return int
     */
    public function getClientId(): int
    {
        return $this->clientId;
    }

    /**
     * @param int $clientId
     * @return SecurityService
     */
    public function setClientId(int $clientId): SecurityService
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * @return int
     */
    public function getPathId(): int
    {
        return $this->pathId;
    }

    /**
     * @param int $pathId
     * @return SecurityService
     */
    public function setPathId(int $pathId): SecurityService
    {
        $this->pathId = $pathId;
        return $this;
    }
}
