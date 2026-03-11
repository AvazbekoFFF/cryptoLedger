<?php

namespace Database\Factories\CryptoBalance;

use App\Models\CryptoBalance\CryptoBalance;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CryptoBalance>
 */
class CryptoBalanceFactory extends Factory
{
    protected $model = CryptoBalance::class;
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'balance' => $this->faker->randomFloat(8, 0, 100),
        ];
    }
}
