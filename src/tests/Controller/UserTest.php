<?php
namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends AbstractApiController
{
    private $token = null;
    
    protected function getToken()
    {
        return $this->token;
    }

    public function testListUserFail()
    {
        /** @var Symfony\Component\HttpFoundation\JsonResponse $response */
        $response = $this->makeRequest(
            'GET',
            '/user',
            [],
            []
        );
        $this->assertEquals(401, $response->getStatusCode());
        $responseJson = json_decode($response->getContent(), true);
        $this->assertIsArray($responseJson);
    }

    public function testListUserFailWithComonUser()
    {
        $this->token = $this->createToken(User::ROLE_WRITER);
        /** @var Symfony\Component\HttpFoundation\JsonResponse $response */
        $response = $this->makeRequest(
            'GET',
            '/user'
        );
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testListUser()
    {
        $this->token = $this->createToken(User::ROLE_ADMINISTRATOR);
        /** @var Symfony\Component\HttpFoundation\JsonResponse $response */
        $response = $this->makeRequest(
            'GET',
            '/user'
        );
        $this->assertEquals(200, $response->getStatusCode());
        $responseJson = json_decode($response->getContent(), true);
        $this->assertIsArray($responseJson);
    }

    private function testGetCurrentUser()
    {
        $token = $this->createToken(User::ROLE_ADMINISTRATOR);
        /** @var Symfony\Component\HttpFoundation\JsonResponse $response */
        $response = $this->makeRequest(
            'GET',
            '/me',
            [],
            [],
            ['Authorization' => $token]
        );
        $this->assertEquals(200, $response->getStatusCode());
        $responseJson = json_decode($response->getContent(), true);
        $this->assertIsArray($responseJson);
    }
}
