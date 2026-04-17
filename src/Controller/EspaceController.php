<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class EspaceController extends AbstractController
{
    #[Route('/espaces/test', name: 'app_espaces_test')]
    public function espaceTest(): JsonResponse
    {
        return $this->json([
            'message' => 'Espace Test'
        ]);
    }
}
