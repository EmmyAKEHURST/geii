<?php

namespace App\Controller\Personnel;

use App\Entity\Compte;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Trait partagé par tous les contrôleurs CRUD de l'Espace Personnel.
 *
 * Fournit :
 *  - getStaffData() : données affichées dans la sidebar pour l'utilisateur connecté ;
 *  - addRoleToCompte() / removeRoleFromCompte() / syncCompteRole() : gestion
 *    idempotente des rôles Symfony d'un Compte quand on rattache ou détache un
 *    profil métier (étudiant, enseignant, personnel, entreprise).
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

    /**
     * Retire un rôle du Compte s'il y figure.
     */
    private function removeRoleFromCompte(Compte $compte, string $role): void
    {
        $current = array_filter($compte->getRoles(), static fn (string $r): bool => $r !== 'ROLE_USER');
        $updated = array_values(array_filter($current, static fn (string $r): bool => $r !== $role));

        if (\count($updated) === \count($current)) {
            return;
        }

        $compte->setRoles($updated);
    }

    /**
     * Synchronise le rôle métier lors d'un changement de rattachement Compte ↔ profil.
     *
     * - Compte retiré du profil → rôle révoqué sur l'ancien compte ;
     * - Compte remplacé → rôle révoqué sur l'ancien, ajouté sur le nouveau ;
     * - Compte ajouté → rôle ajouté sur le nouveau.
     */
    private function syncCompteRole(?Compte $previousCompte, ?Compte $newCompte, string $role): void
    {
        if ($previousCompte !== null && $previousCompte !== $newCompte) {
            $this->removeRoleFromCompte($previousCompte, $role);
        }

        if ($newCompte !== null) {
            $this->addRoleToCompte($newCompte, $role);
        }
    }
}
