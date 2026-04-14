<?php

namespace App\Enum;

enum StatutEnum: string
{
    case OUVERT = 'ouvert';
    case EN_COURS = 'en_cours';
    case TERMINE = 'termine';
}
