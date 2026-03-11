<?php

namespace App\Models\CryptoBalance;

use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property int $user_id
 * @property float $balance
 * @property User $user
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class CryptoBalance extends Model
{
    use HasFactory;

    protected $table = 'crypto_balances';
    protected $fillable = [
        'user_id',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:8',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
