<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/api/register', name: 'api_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
        ): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Missing required fields'], 400); // Bad Request
        }

        $userExist = $userRepository->findOneBy([
            'email'=> $data['email']
        ]);

        if ($userExist) {
            return $this->json(['userExist' => true]);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPseudo($data['pseudo']);
        $user->setRoles(['ROLE_USER']);

        try {
            $user->setPassword($userPasswordHasher->hashPassword($user, $data['password']));
            $entityManager->persist($user);
            $entityManager->flush();

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->headers->set('Access-Control-Allow-Origin', '*'); // Replace with your frontend's origin
            $response->setContent(json_encode(['message' => 'Registration successful']));

            return $this->json(['message' => 'Registration successful']);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Registration failed']);
        }
    }
}
