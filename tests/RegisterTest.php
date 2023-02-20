<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class RegisterTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testRegister(): void
    {
        $response = static::createClient()->request(
            'POST',
            '/api/register',
            [
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'email' => 'emailmail@email50.com',
                    'username' => 'User50',
                    'password' => 'password50',
                ],
            ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }
}
