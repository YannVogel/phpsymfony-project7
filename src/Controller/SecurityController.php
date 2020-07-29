<?php

namespace App\Controller;

use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * Allow a client to obtain a JSON Web Token and a Refresh Token for the api with his credentials.
     *
     * @Route("/login", name="login", methods={"POST"})
     *
     * @SWG\Parameter(
     *     name="username",
     *     in="body",
     *     required=true,
     *     type="string",
     *     description="The mail of the client.",
     *     @SWG\Schema(
     *     type="string",
     *     pattern="^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$",
     *     example={"username" : "client1@bilemo.com"}
     *     )
     * )
     *
     * @SWG\Parameter(
     *     name="password",
     *     in="body",
     *     required=true,
     *     type="string",
     *     description="The password of the client.",
     *     @SWG\Schema(
     *     type="string",
     *     example={"password" : "clientpassword"}
     *     )
     * )
     *
     * @SWG\Response(
     *     response="200",
     *     description="Returns the client id, a JSON Web Token and a Refresh Token."
     * )
     *
     * @SWG\Response(
     *     response="401",
     *     description="The provided credentials are invalid."
     * )
     *
     */
    public function login()
    {
        /* Handled by Symfony & LexikJWTAuthenticationBundle */
    }
}
