<?php

namespace App\Services\CryptoTransaction;

use App\Exceptions\InsufficientBalanceException;
use App\Jobs\ProcessWithdrawalJob;
use App\Models\CryptoBalance\CryptoBalance;
use App\Models\CryptoTransaction\CryptoTransaction;
use App\Models\CryptoTransaction\Enum\TransactionStatus;
use App\Models\CryptoTransaction\Enum\TransactionType;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Throwable;

class WithdrawCryptoBalanceService
{
    private const  COMMISSION_RATE = '0.01';

    /**
     * @throws Throwable|InvalidArgumentException|InsufficientBalanceException — если balance < amount + commission
     */
    public function handle(int $userId, string $amount): CryptoTransaction
    {
        if (bccomp($amount, '0', 8) <= 0) {
            throw new InvalidArgumentException('Сумма должна быть положительной');
        }

        $commission = bcmul($amount, self::COMMISSION_RATE, 8);
        $total      = bcadd($amount, $commission, 8);

        $withdrawal = DB::transaction(function () use ($userId, $amount, $commission, $total) {
            $balance = CryptoBalance::query()
                ->lockForUpdate()
                ->where('user_id', $userId)
                ->firstOrFail();

            if (bccomp((string) $balance->balance, $total, 8) < 0) {
                throw new InsufficientBalanceException();
            }

            $balance->balance = bcsub((string) $balance->balance, $total, 8);
            $balance->save();

            $withdrawal = CryptoTransaction::query()->create([
                'user_id'       => $userId,
                'amount'        => $amount,
                'type'          => TransactionType::WITHDRAWAL->value,
                'status'        => TransactionStatus::PENDING->value,
                'blockchain_tx' => null, // заполнится в ProcessWithdrawalJob после отправки в блокчейн
            ]);

            CryptoTransaction::query()->create([
                'user_id'       => $userId,
                'amount'        => $commission,
                'type'          => TransactionType::COMMISSION->value,
                'status'        => TransactionStatus::CONFIRMED->value,
                'blockchain_tx' => null,
            ]);

            return $withdrawal;
        });

        ProcessWithdrawalJob::dispatch($withdrawal);

        return $withdrawal;
    }
}
