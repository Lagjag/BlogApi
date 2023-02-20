<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class ApiCategoryController extends AbstractController
{
    /**
     * List all categories stored in database.
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns all categories stored in database",
     * )
     */
    #[Route('/categories', name: 'categories', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): Response
    {
        $categories = $doctrine
            ->getRepository(Category::class)
            ->findAll();

        $data = [];

        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
            ];
        }

        return $this->json($data);
    }

    /**
     * Create new category in database
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns all categories stored in database",
     * )
     *
     * @OA\Parameter(
     *     name="name",
     *     in="path",
     *     description="Name of the category",
     *     required=true,
     *
     *     @OA\Schema(type="string")
     * )
     */
    #[Route('/category', name: 'new_category', methods: ['POST'])]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $categoryRequest = json_decode($request->getContent(), true);

        $category = new Category();
        $category->setName($categoryRequest['name']);

        $entityManager->persist($category);
        $entityManager->flush();

        return $this->json('New category inserted in database with name: '.$category->getName());
    }
}
