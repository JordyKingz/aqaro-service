<?php

namespace App\Models\Enums;

enum SubscriptionType: string
{
    case Newsletter = 'Newsletter';
    case Launch = 'Launch';
    case Both = 'Both';
}
