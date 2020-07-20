<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use App\Service\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/clients")
 * @IsGranted("ROLE_USER")
 */
class ClientController extends AbstractController
{
    private SecurityService $securityService;

    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * Allow a client to create a new user related to his client ID
     *
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
        $this->securityService
            ->setClientId($this->getUser()->getId())
            ->setPathId($client->getId());

        if (!$this->securityService->areClientIdsMatching())
        {
            /* If IDs are not matching, return a 404 for security purpose. */
            return $this->securityService->jsonToReturnIfNotFound();
        }

        /* @var User $user */
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $errors = $validator->validate($user);

        if (count($errors)) {
            return $this->securityService->jsonToReturnIfBadRequest();
        }

        $user->setClient($client);
        $manager->persist($user);
        $manager->flush();

        return $this->json(
            [
                'status' => 201,
                'message' => 'Resource created successfully',
                'uri' => '/clients/' . $client->getId() . '/users/' . $user->getId()
            ],
            201
        );
    }

    /**
     * Allow a client to view the details of a particular user.
     *
     * @Route("/{id}/users/{user_id}", name="client_user_detail", methods={"GET"})
     * @Entity("user", expr="repository.find(user_id)")
     * @param Client $client
     * @param User $user
     * @param UserRepository $repository
     * @return JsonResponse
     */
    public function readUser(Client $client, User $user, UserRepository $repository)
    {
        $this->securityService
            ->setClientId($this->getUser()->getId())
            ->setPathId($client->getId());

        if (!$this->securityService->areClientIdsMatching())
        {
            /* If IDs are not matching, return a 404 for security purpose. */
            return $this->securityService->jsonToReturnIfNotFound();
        }

        $data = $repository->findOneBy(["client" => $client, "id" => $user->getId()]);

        if (is_null($data)) {
            return $this->securityService->jsonToReturnIfNotFound();
        }

        return $this->json(
            $data,
            200, [],
            ['groups' => 'detail']);
    }

    /**
     * Allow a client to view the list of all the registered users related to his client ID.
     *
     * @Route("/{id}/users/{page<\d+>?1}", name="client_users_list", methods={"GET"})
     * @param Client $client
     * @param Request $request
     * @param UserRepository $repository
     * @param PaginationService $paginationService
     * @return JsonResponse|RedirectResponse
     */
    public function readUsers(Client $client, Request $request, UserRepository $repository, PaginationService $paginationService)
    {
        $this->securityService
            ->setClientId($this->getUser()->getId())
            ->setPathId($client->getId());

        if (!$this->securityService->areClientIdsMatching())
        {
            /* If IDs are not matching, return a 404 for security purpose. */
            return $this->securityService->jsonToReturnIfNotFound();
        }

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
     * Allow a client to delete a particular user related to his client ID.
     *
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
        $this->securityService
            ->setClientId($this->getUser()->getId())
            ->setPathId($client->getId());

        if (!$this->securityService->areClientIdsMatching())
        {
            /* If IDs are not matching, return a 404 for security purpose. */
            return $this->securityService->jsonToReturnIfNotFound();
        }

        $data = $repository->findOneBy(["client" => $client, "id" => $user->getId()]);

        if (is_null($data)) {
            return $this->securityService->jsonToReturnIfNotFound();
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
