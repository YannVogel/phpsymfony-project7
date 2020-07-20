<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * Allow a client to log to the api with its credentials.
     *
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login()
    {
        /* Handled by Symfony & LexikJWTAuthenticationBundle */
    }
}
