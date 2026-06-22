<?php

namespace App\Enums;

enum FactoryStatus: string
{
    case Idle = 'idle';
    case Running = 'running';
    case Broken = 'broken';
}
