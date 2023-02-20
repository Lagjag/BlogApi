<?php

namespace App\Tests;

class CategoryTest extends LoginTest
{
    public function testGetCategories(): void
    {
        $client = self::createClient();

        $client->loginUser($this->getUserLogin());

        $response = $client->request(
            'GET',
            '/api/categories',
            [
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                    'Accept' => 'application/json',
                ],
            ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertCount(10, $response->toArray());
    }

    public function testInsertCategory(): void
    {
        $client = self::createClient();
        $client->loginUser($this->getUserLogin());
        $category = json_encode($this->setCategoryTest());

        $response = $client->request(
            'POST',
            '/api/category',
            [
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                    'Accept' => 'application/json',
                ],
                'body' => $category,
            ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertJsonStringEqualsJsonString(
            json_encode('New category inserted in database with name: Test'),
            $response->getContent());
    }

    protected function setCategoryTest(): array
    {
        $category = [
            'name' => 'Test',
        ];

        return $category;
    }
}
