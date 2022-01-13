<?php

use App\Entity\User;
use App\Tests\Controller\AbstractApiController;

class PostTest extends AbstractApiController
{

    private $token = null;
    private $idPost = null;

    protected function getToken()
    {
        return $this->token;
    }

    public function testCreateWithoutLogin()
    {
        $this->token = null;
        /** @var Symfony\Component\HttpFoundation\JsonResponse $response */
        $response = $this->makeRequest(
            'POST',
            '/post',
            ['title' => 'Title of the post', 'text' => 'Content'],
            [],
            []
        );
        
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testCreate()
    {
        $this->token = $this->createToken(User::ROLE_WRITER);
        /** @var Symfony\Component\HttpFoundation\JsonResponse $response */
        $response = $this->makeRequest(
            'POST',
            '/post',
            ['title' => 'Title of the post', 'text' => 'Content']
        );
        
        $this->assertEquals(201, $response->getStatusCode());
        $json = json_decode($response->getContent(), true);
        $this->assertIsArray($json);
        $this->idPost = $json['post']['id'];
        $this->assertEquals($json['result'], 'success');
   
        $uri = '/post/' . $this->idPost;
        /** @var Symfony\Component\HttpFoundation\JsonResponse $response */
        $response = $this->makeRequest(
            'PUT',
            $uri,
            ['title' => 'Edited post title', 'text' => 'Content with something']
        );
        
        $this->assertEquals(200, $response->getStatusCode());
        $json = json_decode($response->getContent(), true);
        $this->assertIsArray($json);
        $this->assertEquals($json['result'], 'success');


        /** @var Symfony\Component\HttpFoundation\JsonResponse $response */
        $response = $this->makeRequest(
            'DELETE',
            '/post/' . $this->idPost
        );
        
        $this->assertEquals(202, $response->getStatusCode());
    }

    public function testListMyPosts()
    {
        $this->token = $this->createToken(User::ROLE_WRITER);
        /** @var Symfony\Component\HttpFoundation\JsonResponse $response */
        $response = $this->makeRequest(
            'GET',
            '/post/my-posts'
        );
        $this->assertEquals(200, $response->getStatusCode());
        $json = json_decode($response->getContent(), true);
        $this->assertIsArray($json);
        $this->assertEquals($json['result'], 'success');
    }

    public function testListAllPosts()
    {
        $this->token = null;
        /** @var Symfony\Component\HttpFoundation\JsonResponse $response */
        $response = $this->makeRequest(
            'GET',
            '/post'
        );
        $this->assertEquals(200, $response->getStatusCode());
        $json = json_decode($response->getContent(), true);
        $this->assertIsArray($json);
        $this->assertEquals($json['result'], 'success');
    }
}
