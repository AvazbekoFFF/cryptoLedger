<?php

namespace App\Services\CryptoTransaction;

use App\Exceptions\InsufficientBalanceException;
use App\Models\CryptoBalance\CryptoBalance;
use App\Models\CryptoTransaction\CryptoTransaction;
use App\Models\CryptoTransaction\Enum\TransactionStatus;
use App\Models\CryptoTransaction\Enum\TransactionType;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Throwable;

class CommissionService
{
    /**
     * @throws Throwable|InsufficientBalanceException|InvalidArgumentException
     */
    public function handle(int $userId, string $amount): CryptoTransaction
    {
        if (bccomp($amount, '0', 8) <= 0) {
            throw new InvalidArgumentException('Сумма должна быть положительной');
        }

        return DB::transaction(function () use ($userId, $amount) {
            $balance = CryptoBalance::query()
                ->lockForUpdate()
                ->where('user_id', $userId)
                ->firstOrFail();

            if (bccomp((string) $balance->balance, $amount, 8) < 0) {
                throw new InsufficientBalanceException();
            }

            $balance->balance = bcsub((string) $balance->balance, $amount, 8);
            $balance->save();

            return CryptoTransaction::query()->create([
                'user_id'       => $userId,
                'amount'        => $amount,
                'type'          => TransactionType::COMMISSION->value,
                'status'        => TransactionStatus::CONFIRMED->value,
                'blockchain_tx' => null,
            ]);
        });
    }
}
