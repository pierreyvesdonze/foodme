<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
        private UserRepository $userRepository,
        private JWTTokenManagerInterface $jwtTokenManager,
    ) {
    }

    #[Route('/api/login', name: 'api_login')]
    public function login(Request $request): Response
    {
        $requestData = json_decode($request->getContent(), true);

        // Vérifier si les données d'identification sont présentes
        if (!isset($requestData['email']) || !isset($requestData['password'])) {
            return $this->json([
                'message' => 'Missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->userRepository->findOneBy(['email' => $requestData['email']]);

        // Vérifier si l'utilisateur existe et si le mot de passe est correct
        if (!$user || !$this->passwordEncoder->isPasswordValid($user, $requestData['password'])) {
            return $this->json([
                'message' => 'Invalid email or password',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $email = $user->getEmail();
        $pseudo = $user->getPseudo();

        $token = $this->jwtTokenManager->create($user);

        return $this->json([
            'email' => $email,
            'pseudo' => $pseudo,
            'token' => $token
        ]);
    }
}
