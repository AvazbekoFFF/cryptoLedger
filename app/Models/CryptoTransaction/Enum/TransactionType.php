<?php

namespace App\Models\CryptoTransaction\Enum;

enum TransactionType: string
{
    case DEPOSIT = 'deposit';
    case WITHDRAWAL = 'withdrawal';
    case COMMISSION = 'commission';
}
