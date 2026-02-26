<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction; // Added this line for the relationship

class ReceiptType extends Model
{
    protected $fillable = [
        'name',
        'group',
        'code',
        'keywords',
    ];

    protected $casts = [
        'keywords' => 'array',
    ];

    public function transactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
