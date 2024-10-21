<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Feature extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function feature (): HasOne
    {
        return $this->hasOne(Feature::class, 'user_id', 'id');
    }

    protected $casts = [
        'contents' => 'object'
    ];
}
