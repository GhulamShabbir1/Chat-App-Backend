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
}
