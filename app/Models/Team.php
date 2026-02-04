<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Team extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'teams';

    protected $fillable = [
        'name',
        'description',
        'workspace_id',
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
        'workspace_id',
        'owner_id',
        'member_ids',
        'settings',
        'created_at',
        'updated_at',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class, 'workspace_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, null, 'team_ids', 'user_ids');
    }

    public function channels()
    {
        return $this->hasMany(Channel::class, 'team_id');
    }

    /**
     * Scope a query to only include teams for a specific workspace.
     */
    public function scopeForWorkspace($query, string $workspaceId)
    {
        return $query->where('workspace_id', $workspaceId);
    }

    /**
     * Create a new team for a user, ensuring they are the owner and a member.
     */
    public static function createForUser(array $attributes, User $user): self
    {
        $attributes['owner_id'] = (string) $user->_id;
        $attributes['member_ids'] = [(string) $user->_id];
        $attributes['workspace_id'] = (string) $attributes['workspace_id']; // Ensure string casting
        
        $team = self::create($attributes);
        
        return $team;
    }
}
