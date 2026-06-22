<?php

namespace App\Enums;

enum LocationTier: string
{
    case Neighborhood = 'neighborhood';
    case City = 'city';
    case Metropolis = 'metropolis';
}
