<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = ['created_at'];

    public function user() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function checkout() :HasOne
    {
        return $this->hasOne(Checkout::class, 'checkout_id', 'id');
    }
}
