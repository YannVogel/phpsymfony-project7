<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class SecurityService extends AbstractController
{
    private int $clientId;
    private int $pathId;

    public function areClientIdsMatching() : bool
    {
        return $this->clientId !== $this->pathId ? false : true;
    }

    public function jsonToResponseIfIdsAreNotMatching() : JsonResponse
    {
        return $this->json(
            [
                'status' => 403,
                'message' => 'Forbidden. The client ID indicated in the path is not your ID.',
                'clientId' => $this->clientId
            ],
            403
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