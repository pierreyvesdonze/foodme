<?php

namespace App\Controller;

use App\Service\JwtUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    private $jwtUserService;

    public function __construct(JwtUserService $jwtUserService)
    {
        $this->jwtUserService = $jwtUserService;
    }

    #[Route('/api/user/profile', name: 'api_user_profile', methods: ['GET'])]
    public function profile(Request $request): Response
    {
        try {
            $username = $this->jwtUserService->getUserFromToken($request);
            $user     = $this->jwtUserService->getUser($username);

            if (!$user) {
                return $this->json(['message' => 'User not found'], 404);
            }

           // $serializedUser = $this->jwtUserService->serializeUser($username);

            $userData = [
                'email' => $user->getEmail(),
                'pseudo' => $user->getPseudo()
            ];

            return $this->json($userData);

        } catch (\Exception $e) {

            return $this->json(['message' => $e->getMessage()], 400);
        }
    }
}
