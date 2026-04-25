<?php

namespace App\Enums;

enum ClientStatus: string
{
    case ACTIVE = 'active';
    case PAUSED = 'paused';
    case STOPPED = 'stopped';
}
