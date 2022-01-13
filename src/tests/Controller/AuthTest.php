<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthTest extends WebTestCase
{
    protected function makeRequest(
        string $method,
        string $uri,
        array $params = [],
        array $body = [],
        array $headers = []
    ) {
        $headers['CONTENT_TYPE'] = 'application/json';
        $client = static::createClient();
        $client->request($method, $uri, $params, [], $headers, json_encode($body));
        return $client->getResponse();
    }

    public function testLoginRoleAdministrador()
    {
        /** @var Symfony\Component\HttpFoundation\JsonResponse $response */
        $response = $this->makeRequest(
            'POST',
            '/login',
            ['email' => 'adm@test.com', 'password' => '123'],
        );
        $this->assertEquals(200, $response->getStatusCode());
        $responseJson = json_decode($response->getContent(), true);
        $this->assertIsArray($responseJson);
        $this->assertEquals('success!', $responseJson['message']);
    }

    public function testLoginRoleWriter()
    {
        /** @var Symfony\Component\HttpFoundation\JsonResponse $response */
        $response = $this->makeRequest(
            'POST',
            '/login',
            ['email' => 'writer@test.com', 'password' => '123'],
        );
        $this->assertEquals(200, $response->getStatusCode());
        $responseJson = json_decode($response->getContent(), true);
        $this->assertIsArray($responseJson);
        $this->assertEquals('success!', $responseJson['message']);
    }

    public function testLoginFail()
    {
        $response = $this->makeRequest(
            'POST',
            '/login',
            [],
            ['email' => 'wrtonguser@test.com', 'password' => 'wrongpass']
        );
        $this->assertEquals(401, $response->getStatusCode()); 
    }
}
