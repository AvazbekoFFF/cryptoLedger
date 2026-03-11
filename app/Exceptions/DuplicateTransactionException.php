<?php

namespace App\Exceptions;

use RuntimeException;

class DuplicateTransactionException extends RuntimeException
{
    public function __construct(string $blockchainTx)
    {
        parent::__construct("Транзакция {$blockchainTx} уже была обработана");
    }
}
