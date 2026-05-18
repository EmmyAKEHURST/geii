<?php

namespace App\Enum;

/**
 * Énumère les statuts des projets tuteurés.
 *
 * OUVERT : le projet tuteuré est disponible.
 * EN_COURS : le projet tuteuré est en cours de travail.
 * TERMINE : le projet tuteure est finalisé.
 */
enum StatutProjetTuteure: string
{
    case OUVERT = 'ouvert';
    case EN_COURS = 'en_cours';
    case TERMINE = 'termine';
}
