<?php

namespace Database\Seeders;

use App\Models\CryptoBalance\CryptoBalance;
use App\Models\CryptoTransaction\CryptoTransaction;
use App\Models\CryptoTransaction\Enum\TransactionStatus;
use App\Models\CryptoTransaction\Enum\TransactionType;
use App\Models\User\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        /* @var User $user*/
        $user = User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);

        CryptoBalance::factory()->create([
            'user_id' => $user->id,
            'balance' => 1.24753810,
        ]);

        $transactions = [
            [
                'type'          => TransactionType::DEPOSIT->value,
                'amount'        => 0.50000000,
                'status'        => TransactionStatus::CONFIRMED->value,
                'blockchain_tx' => '0x' . 'a3f1e2d4b5c67890abcdef1234567890abcdef1234567890abcdef1234567890',
            ],
            [
                'type'          => TransactionType::DEPOSIT->value,
                'amount'        => 1.00000000,
                'status'        => TransactionStatus::CONFIRMED->value,
                'blockchain_tx' => '0x' . 'b9e8d7c6f5a4321098fedcba9876543210fedcba9876543210fedcba98765432',
            ],
            [
                'type'          => TransactionType::WITHDRAWAL->value,
                'amount'        => 0.20000000,
                'status'        => TransactionStatus::CONFIRMED->value,
                'blockchain_tx' => '0x' . 'c1d2e3f4a5b6789012345678901234567890abcd1234567890abcdef12345678',
            ],
            [
                'type'          => TransactionType::COMMISSION->value,
                'amount'        => 0.00021500,
                'status'        => TransactionStatus::CONFIRMED->value,
                'blockchain_tx' => '0x' . 'd4e5f6a7b8c9012345678901234567890abcdef1234567890abcdef123456789',
            ],
            [
                'type'          => TransactionType::DEPOSIT->value,
                'amount'        => 0.75300000,
                'status'        => TransactionStatus::CONFIRMED->value,
                'blockchain_tx' => '0x' . 'e7f8a9b0c1d2345678901234567890abcdef1234567890abcdef1234567890ab',
            ],
            [
                'type'          => TransactionType::WITHDRAWAL->value,
                'amount'        => 0.10000000,
                'status'        => TransactionStatus::CONFIRMED->value,
                'blockchain_tx' => '0x' . 'f0a1b2c3d4e5678901234567890abcdef1234567890abcdef1234567890abcde',
            ],
            [
                'type'          => TransactionType::COMMISSION->value,
                'amount'        => 0.00010800,
                'status'        => TransactionStatus::CONFIRMED->value,
                'blockchain_tx' => '0x' . '1a2b3c4d5e6f789012345678901234567890abcdef1234567890abcdef123456',
            ],
            [
                'type'          => TransactionType::DEPOSIT->value,
                'amount'        => 0.30000000,
                'status'        => TransactionStatus::PENDING->value,
                'blockchain_tx' => null,
            ],
            [
                'type'          => TransactionType::WITHDRAWAL->value,
                'amount'        => 0.05000000,
                'status'        => TransactionStatus::FAILED->value,
                'blockchain_tx' => null,
            ],
            [
                'type'          => TransactionType::WITHDRAWAL->value,
                'amount'        => 0.09453810,
                'status'        => TransactionStatus::PENDING->value,
                'blockchain_tx' => null,
            ],
        ];

        foreach ($transactions as $tx) {
            CryptoTransaction::factory()->create([
                'user_id' => $user->id,
                ...$tx,
            ]);
        }
    }
}
