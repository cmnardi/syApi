<?php

namespace App\Controller;

use App\Exception\AccessDeniedException;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;

class UserController extends AbstractFOSRestController
{
    /**
     * @Route("/user", name="user", methods={"GET"})
     * @Rest\View(serializerGroups={"list"})
     */
    public function index(UserRepository $userRepository): View
    {
        $this->denyAccessUnlessGranted('ROLE_ADMINISTRATOR');
        $users = $userRepository->findAll();
        return View::create(['result' => 'ok', 'data' => $users]);
    }

    /**
     * @Route("/me", name="me", methods={"GET"})
     * @Rest\View(serializerGroups={"detail"})
     */
    public function me(): View
    {
        $user = $this->getUser();
        return View::create([$user]);
    }
}
