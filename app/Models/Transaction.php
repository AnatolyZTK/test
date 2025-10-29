<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    const string TYPE_DEPOSIT = 'deposit';
    const string TYPE_WITHDRAW = 'withdraw';
    const string TYPE_TRANSFER_OUT = 'transfer_out';
    const string TYPE_TRANSFER_IN = 'transfer_in';

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'comment',
        'related_user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function relatedUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'related_user_id');
    }
}
