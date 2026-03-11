<?php

namespace Database\Factories\CryptoTransaction;

use App\Models\CryptoTransaction\CryptoTransaction;
use App\Models\CryptoTransaction\Enum\TransactionStatus;
use App\Models\CryptoTransaction\Enum\TransactionType;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CryptoTransaction>
 */
class CryptoTransactionFactory extends Factory
{
    protected $model = CryptoTransaction::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(TransactionType::cases());
        $status = $this->faker->randomElement([
            TransactionStatus::CONFIRMED,
            TransactionStatus::CONFIRMED,
            TransactionStatus::CONFIRMED,
            TransactionStatus::PENDING,
            TransactionStatus::FAILED,
        ]);

        $amount = match ($type) {
            TransactionType::DEPOSIT    => $this->faker->randomFloat(8, 0.001, 5.0),
            TransactionType::WITHDRAWAL => $this->faker->randomFloat(8, 0.001, 4.5),
            TransactionType::COMMISSION => $this->faker->randomFloat(8, 0.000001, 0.005),
        };

        return [
            'user_id'       => User::factory(),
            'amount'        => $amount,
            'type'          => $type->value,
            'status'        => $status->value,
            'blockchain_tx' => $status === TransactionStatus::CONFIRMED
                ? '0x' . $this->faker->regexify('[0-9a-f]{64}')
                : null,
        ];
    }

    public function deposit(): static
    {
        return $this->state(fn () => [
            'type'   => TransactionType::DEPOSIT->value,
            'amount' => $this->faker->randomFloat(8, 0.001, 5.0),
        ]);
    }

    public function withdrawal(): static
    {
        return $this->state(fn () => [
            'type'   => TransactionType::WITHDRAWAL->value,
            'amount' => $this->faker->randomFloat(8, 0.001, 4.5),
        ]);
    }

    public function commission(): static
    {
        return $this->state(fn () => [
            'type'   => TransactionType::COMMISSION->value,
            'amount' => $this->faker->randomFloat(8, 0.000001, 0.005),
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn () => [
            'status'        => TransactionStatus::CONFIRMED->value,
            'blockchain_tx' => '0x' . $this->faker->regexify('[0-9a-f]{64}'),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status'        => TransactionStatus::PENDING->value,
            'blockchain_tx' => null,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status'        => TransactionStatus::FAILED->value,
            'blockchain_tx' => null,
        ]);
    }
}
