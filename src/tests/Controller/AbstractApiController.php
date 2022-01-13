<?php

namespace App\Tests\Controller;

use App\Entity\Profile;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractApiController extends WebTestCase
{
    protected $accounts = [
        User::ROLE_ADMINISTRATOR => [
            'email' => 'adm@test.com',
            'password' => '123'
        ],
        User::ROLE_WRITER => [
            'email' => 'writer@test.com',
            'password' => '123'
        ]
    ];

    protected function createToken(string $role = null)
    {
        $account = $this->accounts[$role];
        $response = $this->makeRequest('POST', '/login', [], $account);
                
        /** @var Symfony\Component\HttpFoundation\JsonResponse $response */
        $response = $this->makeRequest(
            'POST',
            '/login',
            $account
        );
        $content = json_decode($response->getContent(), true);
        if (!isset($content['token'])) {
            throw new \Exception('Login fail');
        }
        return $content['token'];
    }

    protected function makeRequest(
        string $method,
        string $uri,
        array $params = [],
        array $body = [],
        array $headers = []
    ) {
        self::ensureKernelShutdown();
        $headers['CONTENT_TYPE'] = 'application/json';
        if (!is_null($this->getToken())) {
            $headers['HTTP_AUTHORIZATION'] = $this->getToken();
        }
        $client = static::createClient();
        
        $client->request($method, $uri, $params, [], $headers, json_encode($body));
        return $client->getResponse();
    }

    abstract protected function getToken();
}
