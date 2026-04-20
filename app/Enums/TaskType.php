<?php

namespace App\Enums;

enum TaskType: string
{
    case MARKETING = 'marketing';
    case DESIGN = 'design';
    case DEVELOPMENT = 'development';
    case OTHER = 'other';
}
