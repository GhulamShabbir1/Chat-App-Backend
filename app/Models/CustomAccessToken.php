<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

/**
 * Custom access token model for MongoDB
 * Provides token storage and authentication for API requests
 */
class CustomAccessToken extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'custom_access_tokens';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'token',
        'user_id',
        'expires_at',
        'last_used_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'token',
    ];

    /**
     * Get the user that the token belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Find a token by the raw token string.
     */
    public static function findToken($token)
    {
        if (!$token) {
            return null;
        }

        return static::where('token', $token)->first();
    }

    /**
     * Check if the token is expired.
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
