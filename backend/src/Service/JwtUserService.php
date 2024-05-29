<?php

namespace App\Service;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class JwtUserService
{
    private $userRepository;
    private $serializer;

    public function __construct(
        UserRepository $userRepository,
        SerializerInterface $serializer,
    ) {
        $this->userRepository = $userRepository;
        $this->serializer     = $serializer;
    }

    public function getTokenFromRequest(Request $request): ?string
    {
        // Récupérer le header d'autorisation de la requête
        $authorizationHeader = $request->headers->get('Authorization');

        // Vérifier si le header d'autorisation est présent et s'il commence par "Bearer "
        if ($authorizationHeader && str_starts_with($authorizationHeader, 'Bearer ')) {
            // Extraire et retourner le token en supprimant "Bearer " du header
            return substr($authorizationHeader, 7); // 7 est la longueur de "Bearer "
        }

        // Si aucun token n'est trouvé, retourner null
        return null;
    }

    public function getUserFromToken(Request $request): string
    {
        $tokenString = $request->headers->get('Authorization');
        if (!$tokenString || !str_starts_with($tokenString, 'Bearer ')) {
            throw new InvalidTokenException('Invalid token format');
        }

        // Décode le token JWT
        $token        = substr($tokenString, 7); // Enlève 'Bearer ' de la chaîne
        $tokenParts   = explode(".", $token);

        if (count($tokenParts) !== 3) {
            throw new InvalidTokenException('Invalid token format');
        }

        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtPayload   = json_decode($tokenPayload);
        $username     = $jwtPayload->username;

        return $username;
    }

    public function getUser($username)
    {
        $user = $this->userRepository->findOneBy([
            'email' => $username
        ]);

        return $user;
    }

    public function serializeUser(string $username): string
    {
        $user = $this->userRepository->findOneBy([
            'email' => $username
        ]);

        return $this->serializer->serialize($user, 'json');
    }
}
