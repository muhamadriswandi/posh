<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BankAccount;
use App\Models\ReceiptType;
use App\Models\PaymentChannel;

class Transaction extends Model
{
    protected $fillable = [
        'bank_account_id',
        'date',
        'description',
        'amount',
        'type',
        'payment_channel_id',
        'reference',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function bankAccount(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function paymentChannel(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PaymentChannel::class);
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }
}
