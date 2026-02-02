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
}
