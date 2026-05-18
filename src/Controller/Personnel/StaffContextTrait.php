<?php

namespace App\Controller\Personnel;

use App\Entity\Compte;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Trait partagé par tous les contrôleurs CRUD de l'Espace Personnel.
 *
 * Fournit :
 *  - getStaffData() : données affichées dans la sidebar pour l'utilisateur connecté ;
 *  - addRoleToCompte() : ajout idempotent d'un rôle Symfony à un Compte (utilisé
 *    pour propager automatiquement ROLE_ETUDIANT/ENSEIGNANT/PERSONNEL quand on
 *    rattache un Compte à un profil métier).
 *
 * @phpstan-require-extends AbstractController
 */
trait StaffContextTrait
{
    /**
     * @return array<string, string>
     */
    private function getStaffData(): array
    {
        /** @var Compte|null $user */
        $user = $this->getUser();
        $email = $user?->getUserIdentifier() ?? 'personnel@geii.fr';

        return [
            'firstName' => 'Espace',
            'lastName'  => 'Personnel',
            'function'  => $email,
        ];
    }

    /**
     * Ajoute un rôle au Compte s'il n'y figure pas déjà.
     *
     * `Compte::getRoles()` renvoie une union avec ROLE_USER (garantie
     * UserInterface). On retire ce sentinel avant de re-persister afin de
     * stocker uniquement les rôles explicitement gérés.
     */
    private function addRoleToCompte(Compte $compte, string $role): void
    {
        $current = array_filter($compte->getRoles(), static fn (string $r): bool => $r !== 'ROLE_USER');

        if (in_array($role, $current, true)) {
            return;
        }

        $current[] = $role;
        $compte->setRoles(array_values(array_unique($current)));
    }
}
