<?php

namespace App\Tests;

use App\Entity\Author;
use App\Entity\Category;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class BlogPostTest extends LoginTest
{
    use RefreshDatabaseTrait;

    public function testGetPost(): void
    {
        $response = static::createClient()->request('GET', '/api/blogposts_show');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $this->assertCount(30, $response->toArray());
    }

    public function testShowPost(): void
    {
        $client = self::createClient();

        $response = $client->request(
            'GET',
            '/api/blogpost_show/1',
            [
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                    'Accept' => 'application/json',
                ],
            ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertCount(8, $response->toArray());
        $this->assertArrayHasKey('title', $response->toArray());
        $this->assertArrayHasKey('body', $response->toArray());
        $this->assertArrayHasKey('category', $response->toArray());
        $this->assertArrayHasKey('authorName', $response->toArray());
        $this->assertArrayHasKey('authorAddress', $response->toArray());
    }

    public function testInsertPost()
    {
        $client = self::createClient();
        $client->loginUser($this->getUserLogin());
        $post = json_encode($this->setBlogPostForTest());

        $response = $client->request(
            'POST',
            '/api/blogpost',
            [
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                    'Accept' => 'application/json',
                ],
                'body' => $post,
            ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertJsonStringEqualsJsonString(
            json_encode('Post inserted successfully Title for testing'),
            $response->getContent());
    }

    protected function setBlogPostForTest(): array
    {
        $post = [
            'title' => 'Title for testing',
            'body' => 'body for testing',
            'category' => $this->getCategory()->getId(),
            'author' => $this->getAuthor()->getName(),
        ];

        return $post;
    }

    protected function getCategory(): Category
    {
        $container = self::getContainer();
        $category = $container->get('doctrine')->getRepository(Category::class)->find(1);

        return $category;
    }

    protected function getAuthor(): Author
    {
        $container = self::getContainer();
        $author = $container->get('doctrine')->getRepository(Author::class)->find(1);

        return $author;
    }
}
