<?php

namespace App\Models\CryptoTransaction\Enum;

enum TransactionStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case FAILED = 'failed';

}
