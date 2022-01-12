<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMINISTRATOR');
        $users = $userRepository->findAll();
        return $this->json(['result' => 'ok', 'data' => $users]);
    }

    /**
     * @Route("/me", name="me", methods={"GET"})
     */
    public function me(): Response
    {
        $user = $this->getUser();
        return $this->json(['result' => 'ok', 'user' => $user]);
    }
}
