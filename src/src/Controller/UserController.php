<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use App\Entity\User;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

class UserController extends AbstractFOSRestController
{
    /**
     * @Route("/user", name="user", methods={"GET"})
     * @Rest\View(serializerGroups={"list"})
     * @OA\Tag(name="User")
     * @OA\Response(
     *     response=200,
     *     description="List all users",
     *     @Model(type=User::class)
     * )
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
     * @OA\Tag(name="User")
     * @OA\Response(
     *     response=200,
     *     description="Return logged user info",
     *     @Model(type=User::class)
     * )
     */
    public function me(): View
    {
        $user = $this->getUser();
        return View::create([$user]);
    }
}
