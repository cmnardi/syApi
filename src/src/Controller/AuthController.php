<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Firebase\JWT\JWT;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

class AuthController extends AbstractController
{
    /**
    * @Route("/register", name="register", methods={"POST"})
    *
    * @OA\Response(
    *     response=200,
    *     description="Register a new user"
    * )
    * @OA\Parameter(
    *     name="email",in="query", description="The email of the user", @OA\Schema(type="string")
    * )
    * @OA\Parameter(
    *     name="password",in="query",description="The password of the user",@OA\Schema(type="string")
    * )
    * @OA\Parameter(
    *     name="role",in="query", description="The role", @OA\Schema(type="string")
    * )
    * @OA\Tag(name="User")
    */
    public function register(Request $request, UserPasswordEncoderInterface $encoder): View
    {
        $password = $request->get('password');
        $email = $request->get('email');
        $user = new User();
        $user->setPassword($encoder->encodePassword($user, $password));
        $user->setEmail($email);
        $user->setRoles([$request->get('role')]);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return View::create([
            'user' => $user->getEmail()
        ]);
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     * @OA\Parameter(
    *     name="email",in="query", description="The email of the user", @OA\Schema(type="string")
    * )
    * @OA\Parameter(
    *     name="password",in="query",description="The password of the user",@OA\Schema(type="string")
    * )
    * @OA\Tag(name="login")
    * @OA\Response(
    *     response=200,
    *     description="Login user, and return token",
    * )
    * @return Json
    */
    public function login(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $encoder)
    {
        $user = $userRepository->findOneBy(['email'=> $request->get('email'),]);
        if (!$user || !$encoder->isPasswordValid($user, $request->get('password'))) {
            $response = new JsonResponse(['message' => 'email or password is wrong.',]);
            $response->setStatusCode(JsonResponse::HTTP_UNAUTHORIZED);
            return $response;
        }
        $payload = [
            "user" => $user->getUsername(),
            "exp"  => (new \DateTime())->modify("+300 minutes")->getTimestamp(),
        ];

        $jwt = JWT::encode($payload, $this->getParameter('jwt_secret'), 'HS256');
        return $this->json([
            'message' => 'success!',
            'token' => sprintf('Bearer %s', $jwt),
        ]);
    }
}
