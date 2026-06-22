<?php

namespace App\Enums;

enum ResourceType: string
{
    case Steel = 'steel';
    case Aluminum = 'aluminum';
    case Chips = 'chips';
    case Rubber = 'rubber';
    case Glass = 'glass';
    case Paint = 'paint';
}
