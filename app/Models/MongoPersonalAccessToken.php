<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Laravel\Sanctum\Contracts\HasAbilities;

/**
 * A MongoDB-backed personal access token model for Sanctum
 * Provides token storage and authentication for API requests
 */
class MongoPersonalAccessToken extends Model implements HasAbilities
{
    protected $connection = 'mongodb';
    protected $collection = 'personal_access_tokens';
    protected $table = 'personal_access_tokens';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'name',
        'token',
        'abilities',
        'expires_at',
        'tokenable_id',
        'tokenable_type',
        'last_used_at',
    ];

    protected $casts = [
        'abilities' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'token',
    ];

    /**
     * Get the tokenable model that the token belongs to.
     */
    public function tokenable()
    {
        // For MongoDB, we need to manually handle the polymorphic relationship
        // because morphTo doesn't work well with MongoDB's _id
        if (!$this->tokenable_type || !$this->tokenable_id) {
            return null;
        }

        $class = $this->tokenable_type;

        if (!class_exists($class)) {
            return null;
        }

        return $class::find($this->tokenable_id);
    }

    /**
     * Find a token by the raw token string.
     * This is called by Sanctum's Guard to authenticate requests.
     */
    public static function findToken($token)
    {
        if (!$token) {
            return null;
        }

        // Handle pipe-delimited tokens (ID|PlainToken format)
        if (strpos($token, '|') !== false) {
            [$id, $token] = explode('|', $token, 2);
            $hashedToken = hash('sha256', $token);

            $tokenModel = static::find($id);

            if ($tokenModel && hash_equals($tokenModel->token, $hashedToken)) {
                return $tokenModel;
            }
            return null;
        }

        // Handle plain token (just hash and search)
        $hashedToken = hash('sha256', $token);
        return static::where('token', $hashedToken)->first();
    }

    /**
     * Determine if the token has a given ability.
     */
    public function can($ability)
    {
        return in_array('*', $this->abilities ?? []) ||
               array_key_exists($ability, array_flip($this->abilities ?? []));
    }

    /**
     * Determine if the token does not have a given ability.
     */
    public function cant($ability)
    {
        return !$this->can($ability);
    }
}
