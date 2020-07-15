<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/clients")
 */
class ClientController extends AbstractController
{
    /**
     * @Route("/{id}/users", name="client_user_create", methods={"POST"})
     * @param Client $client
     * @param SerializerInterface $serializer
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function createUser(Client $client, SerializerInterface $serializer, Request $request, EntityManagerInterface $manager, ValidatorInterface $validator)
    {
        /* @var User $user */
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $errors = $validator->validate($user);

        if (count($errors)) {
            return $this->json(
                [
                    'status' => 400,
                    'message' => 'Bad request.',
                    'detail' => $errors
                ]
            );
        }

        $user->setClient($client);
        $manager->persist($user);
        $manager->flush();

        return $this->json(
            [
                'status' => 201,
                'message' => 'Resource created successfully',
                'uri' => '/clients/' . $client->getId() . '/users/' . $user->getId()
            ]
        );
    }

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
        $data = $repository->findOneBy(["client" => $client, "id" => $user->getId()]);

        if (is_null($data)) {
            return $this->json(
                [
                'status' => 403,
                'message' => 'Unauthorized to access this resource.'
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

    /**
     * @Route("/{id}/users/{user_id}", name="client_user_delete", methods={"DELETE"})
     * @Entity("user", expr="repository.find(user_id)")
     * @param Client $client
     * @param User $user
     * @param UserRepository $repository
     * @param EntityManagerInterface $manager
     * @return JsonResponse
     */
    public function deleteUser(Client $client, User $user, UserRepository $repository, EntityManagerInterface $manager)
    {
        $data = $repository->findOneBy(["client" => $client, "id" => $user->getId()]);

        if (is_null($data)) {
            return $this->json(
                [
                    'status' => 403,
                    'message' => 'Unauthorized to access this resource.'
                ],
                403
            );
        }

        $manager->remove($data);
        $manager->flush();

        return $this->json(
            [
                'status' => 200,
                'message' => 'Resource deleted successfully'
            ],
            200
        );
    }
}
