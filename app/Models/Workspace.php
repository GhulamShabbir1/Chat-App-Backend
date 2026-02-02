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
}
