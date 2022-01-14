<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;

class PostController extends AbstractFOSRestController
{
    /**
     * @Route("/post", name="post_list", methods={"GET"})
     * @Rest\View(serializerGroups={"list_posts"})
     * @OA\Tag(name="Post")
     * @OA\Response(
     *     response=200,
     *     description="List all posts",
     *     @Model(type=Post::class)
     * )
     *
     */
    public function list(PostRepository $postRepository): View
    {
        try {
            $posts = $postRepository->findAll();
            return View::create([
                'result' => 'success',
                'data' => $posts
            ]);
        } catch (\Exception $ex) {
            return View::create(['message' => $ex->getMessage()], $ex->getCode());
        }
    }

    /**
     * @Route("/post", name="post_create", methods={"POST"})
     * @Rest\View(serializerGroups={"detail"})
     * @OA\Tag(name="Post")
     * @OA\Response(
     *     response=201,
     *     description="Create a new post",
     *     @Model(type=Post::class)
     * )
     * @OA\Parameter(
     *     name="title",in="query", description="Main title of the post", @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     name="text",in="query", description="Content of the post", @OA\Schema(type="string")
     * )
     * @Security(name="Bearer")
     */
    public function create(Request $request): View
    {
        $this->denyAccessUnlessGranted('ROLE_WRITER');
        $user = $this->getUser();
        $post = new Post();
        $post->setAuthor($user)
            ->setText($request->get('text'))
            ->setTitle($request->get('title'));
        $this->persist($post);
        return View::create(['result' => 'success', 'post' => $post], Response::HTTP_CREATED);
    }

    /**
     * @Route("/post/{id}", name="post_update", methods={"PUT"})
     * @Rest\View(serializerGroups={"detail"})
     * @OA\Tag(name="Post")
     * @OA\Response(
     *     response=200,
     *     description="Update a post",
     *     @Model(type=Post::class)
     * )
     * @OA\Parameter(
     *     name="title",in="query", description="The new title of the post", @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     name="text",in="query", description="The new content of the post", @OA\Schema(type="string")
     * )
     * @Security(name="Bearer")
     */
    public function update($id, Request $request, PostRepository $postRepository): View
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        try {
            $post = $this->checkPost($id, $postRepository);
            $post->setText($request->get('text'))->setTitle($request->get('title'));
            $this->persist($post);
            return View::create(['result' => 'success', 'post' => $post], Response::HTTP_OK);
        } catch (\Exception $ex) {
            return View::create(['message' => $ex->getMessage()], $ex->getCode());
        }
    }

    /**
     * @Route("/post/{id}", name="post_delete", methods={"DELETE"})
     * @Rest\View(serializerGroups={"detail"})
     * @OA\Tag(name="Post")
     * @OA\Response(
     *     response=202,
     *     description="Delete a  post",
     *     @Model(type=Post::class)
     * )
     * @Security(name="Bearer")
     */
    public function delete($id, PostRepository $postRepository): View
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        try {
            $post = $this->checkPost($id, $postRepository);

            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
            return View::create(['result' => 'success'], Response::HTTP_ACCEPTED);
        } catch (\Exception $ex) {
            return View::create(['message' => $ex->getMessage()], $ex->getCode());
        }
    }

    /**
     * @Route("/post/my-posts", name="post_list_my_posts", methods={"GET"})
     * @Rest\View(serializerGroups={"list_posts"})
     * @OA\Tag(name="Post")
     * @OA\Response(
     *     response=201,
     *     description="List posts of the logged user",
     *     @Model(type=Post::class)
     * )
     * @Security(name="Bearer")
     */
    public function listMyPosts(PostRepository $postRepository): View
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        try {
            $user = $this->getUser();
            $posts = $postRepository->findByAuthor($user);

            return View::create([
                'result' => 'success',
                'data' => $posts
            ]);
        } catch (\Exception $ex) {
            return View::create(['message' => $ex->getMessage()], $ex->getCode());
        }
    }

    /**
     * Check if the post exists, and check if the owner is the logged user
     * @return Post
     */
    private function checkPost($id, PostRepository $postRepository): Post
    {
        $user = $this->getUser();
        $post = $postRepository->find($id);
        if (is_null($post)) {
            throw new \Exception('Post not fount', JsonResponse::HTTP_NOT_FOUND);
        }
        if ($user->getId() != $post->getAuthor()->getId()) {
            throw new \Exception('You cannot change another\'s author post', JsonResponse::HTTP_BAD_REQUEST);
        }
        return $post;
    }

    /**
     * Persist the post
     * @param Post $post
     * @return void
     */
    private function persist(Post $post): void
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();
    }
}
