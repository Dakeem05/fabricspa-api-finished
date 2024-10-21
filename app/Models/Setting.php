<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function setting (): BelongsTo
    {
        return $this->belongsTo(Setting::class, 'user_id', 'id');
    }

    protected $casts = [
        'notify_clothes_pickup' => 'boolean',
        'notify_clothes_delivered' => 'boolean',
        'notify_clothes_discount_wash' => 'boolean',
        'email_notification' => 'boolean',
        'sms_notification' => 'boolean',
    ];
}
