<?php

namespace App\Services\CryptoTransaction;

use App\Exceptions\DuplicateTransactionException;
use App\Jobs\ConfirmDepositJob;
use App\Models\CryptoTransaction\CryptoTransaction;
use App\Models\CryptoTransaction\Enum\TransactionStatus;
use App\Models\CryptoTransaction\Enum\TransactionType;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Throwable;

class DepositCryptoBalanceService
{
    /**
     * @throws Throwable|DuplicateTransactionException|InvalidArgumentException
     */
    public function handle(int $userId, string $amount, string $blockchainTx): CryptoTransaction
    {
        if (bccomp($amount, '0', 8) <= 0) {
            throw new InvalidArgumentException('Сумма должна быть положительной');
        }

        $transaction = DB::transaction(function () use ($userId, $amount, $blockchainTx) {
            if (CryptoTransaction::query()->where('blockchain_tx', $blockchainTx)->exists()) {
                throw new DuplicateTransactionException($blockchainTx);
            }

            return CryptoTransaction::query()->create([
                'user_id'       => $userId,
                'amount'        => $amount,
                'type'          => TransactionType::DEPOSIT->value,
                'status'        => TransactionStatus::PENDING->value,
                'blockchain_tx' => $blockchainTx,
            ]);
        });

        ConfirmDepositJob::dispatch($transaction);

        return $transaction;
    }
}
