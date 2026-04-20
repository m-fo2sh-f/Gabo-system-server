<?php

namespace App\Enums;

enum PaymentCycle: string
{
    case ONCE = 'once';
    case MONTHLY = 'monthly';
    case WEAKLY = 'weakly';
}
