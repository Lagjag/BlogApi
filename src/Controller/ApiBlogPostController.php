<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\BlogPost;
use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class ApiBlogPostController extends AbstractController
{
    /**
     * List all post stored in database.
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns all post stored in database",
     * )
     */
    #[Route('/blogposts_show', name: 'blogposts_show', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): Response
    {
        $blogs = $doctrine
            ->getRepository(BlogPost::class)
            ->findBy([], ['updatedAt' => 'DESC']);

        $data = [];

        foreach ($blogs as $blog) {
            $data[] = [
                'id' => $blog->getId(),
                'title' => $blog->getTitle(),
                'body' => $blog->getBody(),
                'category' => $blog->getCategory()->getName(),
                'author' => $blog->getAuthor()->getName(),
                'createdAt' => $blog->getCreatedAt(),
                'updatedAt' => $blog->getUpdatedAt(),
            ];
        }

        return $this->json($data);
    }

    /**
     * Creates a new post in database
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns json ",
     * )
     *
     * @OA\Parameter(
     *     name="title",
     *     in="path",
     *     description="Title of the post",
     *     required=true,
     *
     *     @OA\Schema(type="string")
     * )
     *
     * @OA\Parameter(
     *     name="body",
     *     in="path",
     *     description="Body of the post",
     *     required=true,
     *
     *     @OA\Schema(type="string")
     * )
     *
     * @OA\Parameter(
     *     name="category",
     *     in="path",
     *     description="Category of the post",
     *     required=true,
     *
     *     @OA\Schema(type="entity")
     * )
     *
     * @Security(name="Bearer")
     */
    #[Route('/blogpost', name: 'new_blogpost', methods: ['POST'])]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $blogPostRequest = json_decode($request->getContent(), true);

        $category = $this->getCategory($blogPostRequest['category'], $doctrine);
        $author = $this->getAuthor($blogPostRequest['author'], $doctrine);

        $blogPost = new BlogPost();
        $blogPost->setTitle($blogPostRequest['title']);
        $blogPost->setBody($blogPostRequest['body']);
        $blogPost->setCategory($category);
        $blogPost->setAuthor($author);

        $entityManager->persist($blogPost);
        $entityManager->flush();

        return $this->json('Post inserted successfully '.$blogPost->getTitle());
    }

    #[Route('/blogpost_show/{id}', name: 'blogpost_show', methods: ['GET'])]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $blogPost = $doctrine->getRepository(BlogPost::class)->find($id);

        if (! $blogPost) {
            return $this->json('Didnt found post with id: '.$id, 404);
        }

        $data = [
            'id' => $blogPost->getId(),
            'title' => $blogPost->getTitle(),
            'body' => $blogPost->getBody(),
            'category' => $blogPost->getcategory()->getName(),
            'authorName' => $blogPost->getAuthor()->getName(),
            'authorAddress' => $blogPost->getAuthor()->getAddress(),
            'createdAt' => $blogPost->getCreatedAt(),
            'updatedAt' => $blogPost->getUpdatedAt(),
        ];

        return $this->json($data);
    }

    protected function getCategory($id, ManagerRegistry $doctrine): ?Category
    {
        $category = $doctrine->getRepository(Category::class)->find($id);
        if (! $category) {
            $category = new Category();

            return $category;
        }

        return $category;
    }

    protected function getAuthor($name, ManagerRegistry $doctrine): ?Author
    {
        $author = $doctrine->getRepository(Author::class)->findOneBy(['name' => $name]);
        if (! $author) {
            $author = new Author();

            return $author;
        }

        return $author;
    }
}
