<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends AbstractController
{
     /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
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
        return $this->json([
            'user' => $user->getEmail()
        ]);
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
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
