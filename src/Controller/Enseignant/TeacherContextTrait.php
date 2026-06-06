<?php

namespace App\Controller\Enseignant;

use App\Entity\Compte;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Trait partagé par tous les contrôleurs de l'Espace Enseignant.
 *
 * Fournit getTeacherData() : données affichées dans la sidebar pour l'enseignant connecté.
 *
 * @phpstan-require-extends AbstractController
 */
trait TeacherContextTrait
{
    /**
     * @return array<string, string>
     */
    private function getTeacherData(): array
    {
        /** @var Compte|null $user */
        $user = $this->getUser();
        $enseignant = $user?->getEnseignant();

        return [
            'firstName'  => $enseignant?->getPrenom() ?? 'Enseignant',
            'lastName'   => $enseignant?->getNom() ?? '',
            'specialite' => $enseignant?->getSpecialite() ?? '',
        ];
    }
}
