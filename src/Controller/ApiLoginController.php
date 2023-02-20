<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', name: 'api_')]
class ApiLoginController extends AbstractController
{
    /**
    * Login for users
    *
    * @OA\Response(
    *     response=200,
    *     description="Login successfull",
    *     @OA\JsonContent(
    *        type="string"
    *     )
    * )
    * @OA\Parameter(
    *     in="path",
    *     name="body",
    *     @OA\JsonContent(
    *         type="object",
    *         @OA\Property(property="username", type="string"),
    *         @OA\Property(property="password", type="string"),
    *     ),
    * )
    * 
    */
    #[Route('/login_check_api', name: 'login_check_api', methods: ['POST'])]
    public function index(#[CurrentUser] ?User $user): Response
    {
        if (null === $user) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'message' => 'User login',
        ]);
    }
}
