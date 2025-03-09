<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProtectedController extends AbstractController
{
    #[Route('/api/protected', name: 'api_protected', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $user = $this->getUser();

        return $this->json([
            'message' => 'Vous êtes connecté !',
            'user' => $user ? $user->getUserIdentifier() : null,
        ]);
    }
}

