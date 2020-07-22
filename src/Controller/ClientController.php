<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use App\Service\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @Route("/clients")
 * @IsGranted("ROLE_USER")
 */
class ClientController extends AbstractController
{
    private SecurityService $securityService;
    private int $limit = 5;

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
     * @param PaginationService $paginationService
     * @param UserRepository $repository
     * @param CacheInterface $cache
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    public function createUser(Client $client, SerializerInterface $serializer, Request $request, EntityManagerInterface $manager, ValidatorInterface $validator, PaginationService $paginationService, UserRepository $repository, CacheInterface $cache)
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

        $maxPageBeforeFlush = $paginationService->getPages($repository, $this->limit, ["client" => $client]);

        $user->setClient($client);
        $manager->persist($user);
        $manager->flush();

        if ($maxPageBeforeFlush === $paginationService->getPages($repository, $this->limit, ["client" => $client])) {
            $cache->delete('listOfAllUsersForTheClient' . $client->getId() . 'Page' . $maxPageBeforeFlush);
        }

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
     * @param CacheInterface $cache
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    public function readUser(Client $client, User $user, UserRepository $repository, CacheInterface $cache)
    {
        $this->securityService
            ->setClientId($this->getUser()->getId())
            ->setPathId($client->getId());

        if (!$this->securityService->areClientIdsMatching())
        {
            /* If IDs are not matching, return a 404 for security purpose. */
            return $this->securityService->jsonToReturnIfNotFound();
        }

        $data = $cache->get('detailOfTheUser' . $user->getId() . 'forTheClient' . $client->getId(), function() use ($user, $client, $repository) {
            return $repository->findOneBy(["client" => $client, "id" => $user->getId()]);
        });

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
     * @param CacheInterface $cache
     * @return JsonResponse|RedirectResponse
     * @throws InvalidArgumentException
     */
    public function readUsers(Client $client, Request $request, UserRepository $repository, PaginationService $paginationService, CacheInterface $cache)
    {
        $this->securityService
            ->setClientId($this->getUser()->getId())
            ->setPathId($client->getId());

        if (!$this->securityService->areClientIdsMatching())
        {
            /* If IDs are not matching, return a 404 for security purpose. */
            return $this->securityService->jsonToReturnIfNotFound();
        }

        $page = $request->query->get('page');
        $maxPage = $paginationService->getPages($repository, $this->limit, ["client" => $client]);

        if (is_null($page) || $page < 1) {
            $page = 1;
        } else if ($page > $maxPage) {
            return $this->redirectToRoute('client_users_list', ['id' => $client->getId(),'page' => 1], 302);
        }

        $data = $cache->get('listOfAllUsersForTheClient' . $client->getId() . 'Page' . $page, function() use ($client, $page, $repository, $paginationService) {
            return $paginationService->paginateResults($repository, $page, $this->limit, ["client" => $client]);
        });

        return $this->json(
            $data,
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
     * @throws InvalidArgumentException
     */
    public function deleteUser(Client $client, User $user, UserRepository $repository, EntityManagerInterface $manager, PaginationService $paginationService, CacheInterface $cache)
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

        $maxPage = $paginationService->getPages($repository, $this->limit, ["client" => $client]);

        $manager->remove($data);
        $manager->flush();

        for ($i = 1; $i <= $maxPage; $i++) {
            $cache->delete('listOfAllUsersForTheClient' . $client->getId() . 'Page' . $i);
        }

        return $this->json(
            [
                'status' => 200,
                'message' => 'Resource deleted successfully'
            ],
            200
        );
    }
}
