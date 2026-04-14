<?php

namespace App\Enum;

enum StatutProjetTuteure: string
{
    case OUVERT = 'ouvert';
    case EN_COURS = 'en_cours';
    case TERMINE = 'termine';
}
