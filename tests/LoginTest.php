<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class LoginTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    protected ?string $token = null;

    public function setUp(): void
    {
        self::bootKernel();
    }

    protected function getUserLogin(): User
    {
        $container = self::getContainer();
        $user = $container->get('doctrine')->getRepository(User::class)->find(1);

        return $user;
    }

    public function testLogin(): void
    {
        $client = self::createClient();
        $this->setUserForTest();

        $response = $client->request('POST', '/api/login_check_api', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'test@example.com',
                'username' => 'Test',
                'password' => '$3CR3T',
            ],
        ]);

        $token = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertArrayHasKey('token', $token);
    }

    public function testFalseLogin(): void
    {
        $client = self::createClient();

        $response = $client->request('POST', '/api/login_check_api', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'hola@example.com',
                'username' => 'Hola',
                'password' => '$3CR3T',
            ],
        ]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    protected function setUserForTest()
    {
        $container = self::getContainer();

        $user = new User();
        $user->setEmail('test@example.com');
        $user->setUsername('Test');
        $user->setPassword(
            $container->get('security.user_password_hasher')->hashPassword($user, '$3CR3T')
        );

        $manager = $container->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->flush();
    }
}
