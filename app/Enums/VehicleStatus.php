<?php

namespace App\Enums;

enum VehicleStatus: string
{
    case Idle = 'idle';
    case InTransit = 'in_transit';
    case Returning = 'returning';
}
