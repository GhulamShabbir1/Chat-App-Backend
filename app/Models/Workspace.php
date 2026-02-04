<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Workspace extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'workspaces';

    protected $fillable = [
        'name',
        'description',
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
        'owner_id',
        'member_ids',
        'settings',
        'created_at',
        'updated_at',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, null, 'workspace_ids', 'user_ids');
    }

    public function teams()
    {
        return $this->hasMany(Team::class, 'workspace_id');
    }

    /**
     * Create a new workspace for a user.
     */
    public static function createForUser(array $attributes, User $user): self
    {
        $attributes['owner_id'] = (string) $user->_id;
        $attributes['member_ids'] = [];
        $attributes['settings'] = [];

        return self::create($attributes);
    }

    /**
     * Add a member to the workspace.
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
     * Remove a member from the workspace.
     */
    public function removeMember(string $userId): void
    {
        $memberIds = $this->member_ids ?? [];
        $memberIds = array_filter($memberIds, fn($id) => $id != $userId);
        $this->member_ids = array_values($memberIds);
        $this->save();
    }
}
