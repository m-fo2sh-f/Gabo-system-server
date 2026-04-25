<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case INSTAPAY = 'instapay';
    case VODAFONE_CASH = 'vodafone_cash';
    case OTHER = 'other';
}
