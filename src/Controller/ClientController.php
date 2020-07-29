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
use Swagger\Annotations as SWG;
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
 *
 * @SWG\Parameter(
 *     name="id",
 *     in="path",
 *     type="integer",
 *     description="The id of the client."
 * )
 *
 * @SWG\Parameter(
 *     name="Authorization",
 *     in="header",
 *     required=true,
 *     type="string",
 *     default="Bearer Token",
 *     description="Bearer Token"
 * )
 *
 * @SWG\Response(
 *     response="401",
 *     description="The client is not authenticated."
 * )
 *
 * @SWG\Response(
 *     response="405",
 *     description="HTTP method not allowed."
 * )
 */
class ClientController extends AbstractController
{
    private PaginationService $paginationService;
    private CacheInterface $cache;
    private SecurityService $securityService;
    private int $limit = 5;

    public function __construct(SecurityService $securityService, CacheInterface $cache, PaginationService $paginationService)
    {
        $this->securityService = $securityService;
        $this->cache = $cache;
        $this->paginationService = $paginationService;
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
     * @param UserRepository $repository
     * @return JsonResponse
     * @throws InvalidArgumentException
     *
     * @SWG\Parameter(
     *     name="civility",
     *     in="body",
     *     required=true,
     *     type="string",
     *     description="The civility of the user.",
     *     @SWG\Schema(
     *     type="string",
     *     pattern="^m|f$",
     *     example={"civility" : "m"}
     *     )
     * )
     *
     * @SWG\Parameter(
     *     name="firstName",
     *     in="body",
     *     required=true,
     *     type="string",
     *     description="The first name of the user.",
     *     @SWG\Schema(
     *     type="string",
     *     pattern="^[a-zA-Z -éèàç]+$",
     *     example={"firstName" : "Martin"}
     *     )
     * )
     *
     * @SWG\Parameter(
     *     name="lastName",
     *     in="body",
     *     required=true,
     *     type="string",
     *     description="The last name of the user.",
     *     @SWG\Schema(
     *     type="string",
     *     pattern="^[a-zA-Z -éèàç]+$",
     *     example={"lastName" : "Dupont"}
     *     )
     * )
     *
     * @SWG\Parameter(
     *     name="age",
     *     in="body",
     *     required=true,
     *     type="integer",
     *     description="The age of the user.",
     *     @SWG\Schema(
     *     type="string",
     *     pattern="^\d+$",
     *     example={"age" : 37}
     *     )
     * )
     *
     * @SWG\Parameter(
     *     name="city",
     *     in="body",
     *     required=true,
     *     type="string",
     *     description="The city of the user.",
     *     @SWG\Schema(
     *     type="string",
     *     pattern="^[a-zA-Z -éèàç']+$",
     *     example={"city" : "Paris"}
     *     )
     * )
     *
     * @SWG\Parameter(
     *     name="mail",
     *     in="body",
     *     required=true,
     *     type="string",
     *     description="The mail of the user.",
     *     @SWG\Schema(
     *     type="string",
     *     pattern="^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$",
     *     example={"mail" : "user@mail.com"}
     *     )
     * )
     *
     * @SWG\Response(
     *     response="201",
     *     description="User created."
     * )
     *
     * @SWG\Response(
     *     response="400",
     *     description="There are errors on given fields."
     * )
     *
     * @SWG\Response(
     *     response="404",
     *     description="The id provided is not the client's id."
     * )
     */
    public function createUser(Client $client, SerializerInterface $serializer, Request $request, EntityManagerInterface $manager, ValidatorInterface $validator, UserRepository $repository)
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

        $maxPageBeforeFlush = $this->paginationService->getPages($repository, $this->limit, ["client" => $client]);

        $user->setClient($client);
        $manager->persist($user);
        $manager->flush();

        if ($maxPageBeforeFlush === $this->paginationService->getPages($repository, $this->limit, ["client" => $client])) {
            $this->cache->delete('listOfAllUsersForTheClient' . $client->getId() . 'Page' . $maxPageBeforeFlush);
        }

        return $this->securityService->jsonToReturnIfUserCreated($client->getId(), $user->getId());
    }

    /**
     * Allow a client to view the list of all the registered users related to his client ID.
     *
     * @Route("/{id}/users", name="client_users_list", methods={"GET"})
     * @param Client $client
     * @param Request $request
     * @param UserRepository $repository
     * @return JsonResponse|RedirectResponse
     * @throws InvalidArgumentException
     *
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     type="integer",
     *     description="A specific page of the users' list.",
     *     default=1
     * )
     *
     * @SWG\Response(
     *     response="200",
     *     description="Returns the (paginated) user's list belonging to the client."
     * )
     *
     * @SWG\Response(
     *     response="404",
     *     description="The id provided is not the client's id."
     * )
     */
    public function readUsers(Client $client, Request $request, UserRepository $repository)
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
        $maxPage = $this->paginationService->getPages($repository, $this->limit, ["client" => $client]);

        $page = $this->paginationService->checkPageValue($page, $maxPage);

        return $this->json(
            $this->cache->get('listOfAllUsersForTheClient' . $client->getId() . 'Page' . $page,
                function() use ($client, $page, $repository) {
                    return $this->paginationService->paginateResults($repository, $page, $this->limit, ["client" => $client]);
                }),
            200, [],
            ['groups' => 'list']);
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
     * @throws InvalidArgumentException
     *
     * @SWG\Parameter(
     *     name="user_id",
     *     in="path",
     *     type="integer",
     *     description="The id of the user."
     * )
     *
     * @SWG\Response(
     *     response="200",
     *     description="Returns the details of the user."
     * )
     *
     * @SWG\Response(
     *     response="404",
     *     description="The id provided is not the client's id or the provided user_id is not associated to any user belonging to the client."
     * )
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

        $data = $this->cache->get('detailOfTheUser' . $user->getId() . 'forTheClient' . $client->getId(),
            function() use ($user, $client, $repository) {
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
     *
     * @SWG\Parameter(
     *     name="user_id",
     *     in="path",
     *     type="integer",
     *     description="The id of the user."
     * )
     *
     * @SWG\Response(
     *     response="200",
     *     description="User deleted successfully."
     * )
     *
     * @SWG\Response(
     *     response="404",
     *     description="The id provided is not the client's id or the provided user_id is not associated to any user belonging to the client."
     * )
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

        $maxPage = $this->paginationService->getPages($repository, $this->limit, ["client" => $client]);

        $manager->remove($data);
        $manager->flush();

        for ($i = 1; $i <= $maxPage; $i++) {
            $this->cache->delete('listOfAllUsersForTheClient' . $client->getId() . 'Page' . $i);
        }

        return $this->securityService->jsonToReturnIfResourceDeleted();
    }
}
