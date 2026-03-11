<?php

namespace App\Jobs;

use App\Models\CryptoBalance\CryptoBalance;
use App\Models\CryptoTransaction\CryptoTransaction;
use App\Models\CryptoTransaction\Enum\TransactionStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Throwable;


class ConfirmDepositJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;
    public int $backoff = 60;

    public function __construct(private readonly CryptoTransaction $transaction) {}

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        DB::transaction(function () {
            $transaction = CryptoTransaction::query()
                ->lockForUpdate()
                ->findOrFail($this->transaction->id);

            if ($transaction->status !== TransactionStatus::PENDING->value) {
                return;
            }

            $confirmed = true; //TODO подтерждение здесь

            if ($confirmed) {
                $balance = CryptoBalance::query()
                    ->lockForUpdate()
                    ->firstOrCreate(
                        ['user_id' => $transaction->user_id],
                        ['balance' => '0.00000000'],
                    );

                $balance->balance = bcadd((string) $balance->balance, (string) $transaction->amount, 8);
                $balance->save();

                $transaction->status = TransactionStatus::CONFIRMED->value;
            } else {
                $transaction->status = TransactionStatus::FAILED->value;
            }

            $transaction->save();
        });
    }

    public function failed(Throwable $e): void
    {
        CryptoTransaction::query()
            ->where('id', $this->transaction->id)
            ->where('status', TransactionStatus::PENDING->value)
            ->update(['status' => TransactionStatus::FAILED->value]);
    }
}
