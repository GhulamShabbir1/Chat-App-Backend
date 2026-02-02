<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Laravel\Sanctum\HasApiTokens;
use MongoDB\Laravel\Eloquent\Model;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasApiTokens;

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
     * Get the user's personal access tokens.
     */
    public function tokens()
    {
        return $this->morphMany(MongoPersonalAccessToken::class, 'tokenable');
    }

    /**
     * Create a new personal access token for the user.
     */
    public function createToken($name, $abilities = ['*'], $expiresAt = null)
    {
        $plainTextToken = \Illuminate\Support\Str::random(40);
        $hashedToken = hash('sha256', $plainTextToken);

        // Create token data
        $tokenData = [
            'name' => $name,
            'token' => $hashedToken,
            'abilities' => $abilities,
            'tokenable_id' => (string) ($this->_id ?? $this->id),
            'tokenable_type' => self::class,
        ];

        if ($expiresAt) {
            $tokenData['expires_at'] = $expiresAt;
        }

        // Create the token in MongoDB
        $token = MongoPersonalAccessToken::create($tokenData);

        // MongoDB doesn't immediately populate _id in attributes after create
        // Query to get the token with _id populated
        $token = MongoPersonalAccessToken::where('token', $hashedToken)->first();
        $tokenId = (string) $token->_id;

        // Return the token with the plain text version INCLUDING the ID
        // Format: {token_id}|{plain_text_token}
        return (object) [
            'accessToken' => $token,
            'plainTextToken' => $tokenId . '|' . $plainTextToken,
        ];
    }
}
