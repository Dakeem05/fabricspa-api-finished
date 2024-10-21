<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Description extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function user (): BelongsTo
    {
        return $this->belongsTo(User::class ,'user_id', 'id');
    }

    public function feature (): BelongsTo
    {
        return $this->belongsTo(Feature::class ,'feature_id', 'id');
    }

    protected $casts = [
        'colour' => 'object'
    ];
}
