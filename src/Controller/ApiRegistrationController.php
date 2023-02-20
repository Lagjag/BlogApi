<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class ApiRegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function index(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $em = $doctrine->getManager();
        $decoded = json_decode($request->getContent());

        $email = $decoded->email;
        $userName = $decoded->username;
        $plaintextPassword = $decoded->password;

        $user = new User();
        $author = new Author();
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $user->setEmail($email);
        $user->setUsername($userName);
        try {
            $em->persist($user);
            $em->flush();

            $user = $doctrine->getRepository(User::class)->findOneBy(['username' => $userName]);

            $author->setName($userName);
            $author->setUser($user);
            $em->persist($author);
            $em->flush();
        } catch (UniqueConstraintViolationException $e) {
            return $this->json('User resgistered, try with another');
        }

        return $this->json('Usuario registrado correctamente');
    }
}
