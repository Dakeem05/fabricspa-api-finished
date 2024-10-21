<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\Api\V1\ApiResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'google_id',
        'phone',
        'email_verified_at',
        'promo_codes',
        'role',
        'cart_total',
        'dob',
        'username',
        'picture',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function description (): HasOne
    {
        return $this->hasOne(Description::class, 'user_id', 'id');
    }

    public function cart (): HasOne
    {
        return $this->hasOne(Cart::class, 'user_id', 'id');
    }

    public function forgot_otp ():HasOne
    {
        return $this->hasOne(ForgotPasswordOtp::class);
    }

    public function sendApiEmailForgotPasswordNotification()
    {
    //    $this->hasOne(ForgotPasswordOtp::class);
       $this->notify(new ApiResetPassword);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'promo_codes' =>'object'
        // 'dob' => 'date',
    ];

    public function orders (): HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
