<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Checkout extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user (): BelongsTo
    {
        return $this->belongsTo(User::class ,'user_id', 'id');
    }

    public function order (): BelongsTo
    {
        return $this->belongsTo(Order::class ,'checkout_id', 'id');;
    }

    protected $casts = [
        'description_id' => 'object',
        'cart_id' => 'object',
        'features' => 'object',
        'is_verified' => 'boolean',
    ];
}
