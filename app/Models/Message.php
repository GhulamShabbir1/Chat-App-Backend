<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Message extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'messages';

    protected $fillable = [
        'content',
        'sender_id',
        'channel_id',
        'receiver_id',
        'type',
        'attachment_id',
        'edited_at',
        'deleted_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'edited_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $visible = [
        '_id',
        'content',
        'sender_id',
        'channel_id',
        'receiver_id',
        'type',
        'attachment_id',
        'edited_at',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function attachment()
    {
        return $this->belongsTo(FileAttachment::class, 'attachment_id');
    }

    public function isDirectMessage()
    {
        return $this->type === 'direct';
    }

    public function isChannelMessage()
    {
        return $this->type === 'channel';
    }
}
