<?php

use App\Entity\User;
use App\Tests\Controller\AbstractApiController;

class PostTest extends AbstractApiController
{

    private $token = null;

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
    }

    public function testUpdate()
    {
        $this->token = $this->createToken(User::ROLE_WRITER);
        /** @var Symfony\Component\HttpFoundation\JsonResponse $response */
        $response = $this->makeRequest(
            'PUT',
            '/post/1',
            ['title' => 'Edited post title', 'text' => 'Content with something']
        );
        
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDelete()
    {
        $this->token = $this->createToken(User::ROLE_WRITER);
        /** @var Symfony\Component\HttpFoundation\JsonResponse $response */
        $response = $this->makeRequest(
            'DELETE',
            '/post/1'
        );
        
        $this->assertEquals(202, $response->getStatusCode());
    }
}
