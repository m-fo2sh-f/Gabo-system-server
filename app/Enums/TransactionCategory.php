<?php

namespace App\Enums;

enum TransactionCategory: string
{
    case TASK_PAYMENT = 'task_payment';
    case MANUAL_COLLECTION = 'manual_collection';
    case GENERAL_INCOME = 'general_income';
    case SALARY = 'salary';
    case ADS = 'ads';
    case RENT = 'rent';
    case OTHER = 'other';
}
