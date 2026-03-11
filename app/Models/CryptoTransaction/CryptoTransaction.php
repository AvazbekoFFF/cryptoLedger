<?php

namespace App\Models\CryptoTransaction;

use App\Models\User\User;
use Carbon\Carbon;
use Database\Factories\CryptoTransaction\CryptoTransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property int $user_id
 * @property float $amount
 * @property string $type
 * @property string $status
 * @property string $blockchain_tx
 * @property User $user
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class CryptoTransaction extends Model
{
    use HasFactory;

    protected $table = 'crypto_transactions';
    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'status',
        'blockchain_tx',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
