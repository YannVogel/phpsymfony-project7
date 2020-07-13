<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/clients")
 */
class ClientController extends AbstractController
{
    /**
     * @Route("/{id}/users/{user_id}", name="client_user_detail", methods={"GET"})
     * @Entity("user", expr="repository.find(user_id)")
     * @param Client $client
     * @param User $user
     * @param UserRepository $repository
     * @return JsonResponse
     */
    public function readUser(Client $client, User $user, UserRepository $repository)
    {
        $data = $repository->findBy(["client" => $client, "id" => $user->getId()]);

        if (empty($data)) {
            return $this->json(
                [
                'status' => 403,
                'message' => 'Unauthorized to access this ressource.'
                ],
                403
            );
        }

        return $this->json(
            $data,
            200, [],
            ['groups' => 'detail']);
    }

    /**
     * @Route("/{id}/users/{page<\d+>?1}", name="client_users_list", methods={"GET"})
     * @param Client $client
     * @param Request $request
     * @param UserRepository $repository
     * @param PaginationService $paginationService
     * @return JsonResponse|RedirectResponse
     */
    public function readUsers(Client $client, Request $request, UserRepository $repository, PaginationService $paginationService)
    {
        $limit = 5;
        $page = $request->query->get('page');
        $maxPage = $paginationService->getPages($repository, $limit, ["client" => $client]);

        if (is_null($page) || $page < 1) {
            $page = 1;
        } else if ($page > $maxPage) {
            return $this->redirectToRoute('client_users_list', ['id' => $client->getId(),'page' => 1], 302);
        }

        return $this->json(
            $paginationService->paginateResults($repository, $page, $limit, ["client" => $client]),
            200, [],
            ['groups' => 'list']);
    }
}
