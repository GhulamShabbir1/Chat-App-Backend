<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class PasswordResetToken extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'password_reset_tokens';
    protected $primaryKey = 'email';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
