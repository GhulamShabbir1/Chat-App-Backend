<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Channel extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'channels';

    protected $fillable = [
        'name',
        'description',
        'team_id',
        'type',
        'owner_id',
        'member_ids',
        'settings',
    ];

    protected $casts = [
        'member_ids' => 'array',
        'settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $visible = [
        '_id',
        'name',
        'description',
        'team_id',
        'type',
        'owner_id',
        'member_ids',
        'settings',
        'created_at',
        'updated_at',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, null, 'channel_ids', 'user_ids');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'channel_id');
    }

    public function isPrivate()
    {
        return $this->type === 'private';
    }

    public function isPublic()
    {
        return $this->type === 'public';
    }

    /**
     * Create a new channel for a user.
     */
    public static function createForUser(array $attributes, User $user): self
    {
        $attributes['owner_id'] = (string) $user->_id;
        $attributes['member_ids'] = ($attributes['type'] ?? '') === 'private' ? [(string) $user->_id] : [];
        $attributes['settings'] = [];
        // Ensure team_id is string
        if (isset($attributes['team_id'])) {
            $attributes['team_id'] = (string) $attributes['team_id'];
        }

        return self::create($attributes);
    }

    /**
     * Add a member to the channel.
     */
    public function addMember(string $userId): bool
    {
        $memberIds = $this->member_ids ?? [];
        if (in_array($userId, $memberIds)) {
            return false;
        }

        $memberIds[] = $userId;
        $this->member_ids = $memberIds;
        $this->save();

        return true;
    }

    /**
     * Remove a member from the channel.
     */
    public function removeMember(string $userId): void
    {
        $memberIds = $this->member_ids ?? [];
        $memberIds = array_filter($memberIds, fn($id) => $id != $userId);
        $this->member_ids = array_values($memberIds);
        $this->save();
    }
}
