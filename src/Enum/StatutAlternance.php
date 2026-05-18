<?php

namespace App\Enum;

/**
 * Enumération de statut d'alternance.
 * ACTIVE : l'offre d'alternance est disponible.
 * POURVUE : l'offre d'alternance est occupée.
 * EXPIREE : l'offre d'alternance n'est plus disponible.
 */
enum StatutAlternance: string
{
    case ACTIVE = 'active';
    case POURVUE = 'pourvue';
    case EXPIREE = 'expiree';
}
