<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class PostController extends AbstractController
{
    /**
     * @Route("/post", name="post_create", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_WRITER');
        $user = $this->getUser();
        $post = new Post();
        $post->setAuthor($user)
            ->setText($request->get('text'))
            ->setTitle($request->get('title'));
        $this->persist($post);
        return $this->json(['post' => $post]);
    }

    /**
     * @Route("/post/{id}", name="post_update", methods={"PUT"})
     */
    public function update($id, Request $request, PostRepository $postRepository): Response
    {
        try {
            $post = $this->checkPost($id, $postRepository);
            $post->setText($request->get('text'))->setTitle($request->get('title'));
            $this->persist($post);
            return $this->json(['post' => $post]);
        } catch (\Exception $ex) {
            $response = new JsonResponse(['message' => $ex->getMessage()]);
            $response->setStatusCode($ex->getCode());
            return $response;
        }
    }

    /**
     * @Route("/post/{id}", name="post_delete", methods={"DELETE"})
     */
    public function delete($id, PostRepository $postRepository): Response
    {
        try {
            $post = $this->checkPost($id, $postRepository);

            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
            return $this->json([
                'result' => 'success'
            ]);
        } catch (\Exception $ex) {
            $response = new JsonResponse(['message' => $ex->getMessage()]);
            $response->setStatusCode($ex->getCode());
            return $response;
        }
    }

    /**
     * @Route("/post/my-posts", name="post_list_my_posts", methods={"GET"})
     */
    public function listMyPosts(PostRepository $postRepository): Response
    {
        try {
            $user = $this->getUser();
            $posts = $postRepository->findByAuthor($user);
            
            return $this->json([
                'result' => 'success',
                'data' => $posts
            ]);
        } catch (\Exception $ex) {
            die($ex->getMessage());
            $response = new JsonResponse(['message' => $ex->getMessage()]);
            $response->setStatusCode($ex->getCode());
            return $response;
        }
    }

    private function checkPost($id, PostRepository $postRepository): Post
    {
        $user = $this->getUser();
        $post = $postRepository->find($id);
        if (is_null($post)) {
            throw new \Exception('Post not fount', JsonResponse::HTTP_NOT_FOUND );
        }
        if ($user->getId() != $post->getAuthor()->getId()) {
            throw new \Exception('You cannot change another\'s author post', JsonResponse::HTTP_BAD_REQUEST);
        }
        return $post;
    }

    private function persist(Post $post)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();
    }
}
