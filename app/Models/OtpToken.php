<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class OtpToken extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'otp_tokens';
    protected $primaryKey = 'email';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'email',
        'otp',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
