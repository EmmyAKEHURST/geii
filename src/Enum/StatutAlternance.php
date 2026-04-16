<?php

namespace App\Enum;

enum StatutAlternance: string
{
    case ACTIVE = 'active';
    case POURVUE = 'pourvue';
    case EXPIREE = 'expiree';
}
