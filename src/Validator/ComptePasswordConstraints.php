<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Politique unique de mot de passe pour tous les comptes
 * (inscription publique, création et réinitialisation par le Personnel).
 */
final class ComptePasswordConstraints
{
    public const MIN_LENGTH = 13;

    public const MAX_LENGTH = 4096;

    /**
     * @return list<Constraint>
     */
    public static function rules(): array
    {
        return [
            new NotBlank(message: 'Veuillez saisir un mot de passe.'),
            new Length(
                min: self::MIN_LENGTH,
                max: self::MAX_LENGTH,
                minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
            ),
            new Regex(
                pattern: '/[0-9]/',
                message: 'Le mot de passe doit contenir au moins un chiffre.',
            ),
            new Regex(
                pattern: '/[A-Z]/',
                message: 'Le mot de passe doit contenir au moins une lettre majuscule.',
            ),
            new Regex(
                pattern: '/[a-z]/',
                message: 'Le mot de passe doit contenir au moins une lettre minuscule.',
            ),
            new Regex(
                pattern: '/[^A-Za-z0-9]/',
                message: 'Le mot de passe doit contenir au moins un caractère spécial.',
            ),
        ];
    }
}
