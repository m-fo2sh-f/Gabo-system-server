<?php

namespace App\Enums;

enum EmployeeStatus: string
{
    case ACTIVE = 'active';
    case PAUSED = 'paused';
    case STOPPED = 'stopped';
}
