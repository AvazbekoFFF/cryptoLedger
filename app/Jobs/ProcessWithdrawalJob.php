<?php

namespace App\Jobs;

use App\Models\CryptoBalance\CryptoBalance;
use App\Models\CryptoTransaction\CryptoTransaction;
use App\Models\CryptoTransaction\Enum\TransactionStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProcessWithdrawalJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 30;

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


            $success = true;
            $txHash  = '0x' . bin2hex(random_bytes(32));

            if ($success) {
                $transaction->status        = TransactionStatus::CONFIRMED->value;
                $transaction->blockchain_tx = $txHash;
                $transaction->save();
            } else {
                $this->refundBalance($transaction);
                $transaction->status = TransactionStatus::FAILED->value;
                $transaction->save();
            }
        });
    }

    /**
     * @throws Throwable
     */
    public function failed(Throwable $e): void
    {
        DB::transaction(function () {
            $transaction = CryptoTransaction::query()
                ->lockForUpdate()
                ->findOrFail($this->transaction->id);

            if ($transaction->status !== TransactionStatus::PENDING->value) {
                return;
            }

            $this->refundBalance($transaction);
            $transaction->status = TransactionStatus::FAILED->value;
            $transaction->save();
        });
    }

    private function refundBalance(CryptoTransaction $transaction): void
    {
        $balance = CryptoBalance::query()
            ->lockForUpdate()
            ->where('user_id', $transaction->user_id)
            ->firstOrFail();

        $balance->balance = bcadd((string) $balance->balance, (string) $transaction->amount, 8);
        $balance->save();
    }
}
