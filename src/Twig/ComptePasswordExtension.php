<?php

namespace App\Twig;

use App\Validator\ComptePasswordConstraints;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

/**
 * Expose la politique de mot de passe aux templates Twig (indicateur visuel, etc.).
 */
class ComptePasswordExtension extends AbstractExtension implements GlobalsInterface
{
    public function getGlobals(): array
    {
        return [
            'compte_password_min_length' => ComptePasswordConstraints::MIN_LENGTH,
        ];
    }
}
