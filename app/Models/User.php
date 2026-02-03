<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use MongoDB\Laravel\Eloquent\Model;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture',
        'status',
        'last_seen_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected $visible = [
        '_id',
        'name',
        'email',
        'profile_picture',
        'status',
        'last_seen_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'password' => 'hashed',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function workspaces()
    {
        return $this->hasMany(Workspace::class, 'owner_id');
    }

    public function workspaceMemberships()
    {
        return $this->belongsToMany(
            Workspace::class,
            null,
            'user_ids',
            'workspace_ids'
        );
    }

    public function teamMemberships()
    {
        return $this->belongsToMany(
            Team::class,
            null,
            'user_ids',
            'team_ids'
        );
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get the user's custom access tokens.
     */
    public function customTokens()
    {
        return $this->hasMany(CustomAccessToken::class, 'user_id');
    }

    /**
     * Create a new custom access token for the user.
     */
    public function createCustomToken($expiresAt = null)
    {
        // Generate a random token using mt_rand
        $token = $this->generateRandomToken(64); // 64 characters long

        // Create token data
        $tokenData = [
            'token' => $token,
            'user_id' => (string) ($this->_id ?? $this->id),
        ];

        if ($expiresAt) {
            $tokenData['expires_at'] = $expiresAt;
        }

        // Create the token in MongoDB
        $customToken = CustomAccessToken::create($tokenData);

        return $token;
    }

    /**
     * Generate a random token using mt_rand.
     */
    private function generateRandomToken($length = 64)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
