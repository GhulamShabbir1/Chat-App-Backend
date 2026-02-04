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

    /**
     * Scope to filter messages.
     */
    public function scopeFilter($query, array $filters)
    {
        // Implementation for generic filters if needed
        return $query;
    }

    /**
     * Scope for channel messages.
     */
    public function scopeForChannel($query, string $channelId)
    {
        return $query->where('channel_id', $channelId)
                     ->where('type', 'channel')
                     ->whereNull('deleted_at');
    }

    /**
     * Scope for direct messages between two users.
     */
    public function scopeForDirect($query, string $userId1, string $userId2)
    {
        return $query->where('type', 'direct')
                     ->where(function ($q) use ($userId1, $userId2) {
                         $q->where(function ($sub) use ($userId1, $userId2) {
                             $sub->where('sender_id', $userId1)
                                 ->where('receiver_id', $userId2);
                         })->orWhere(function ($sub) use ($userId1, $userId2) {
                             $sub->where('sender_id', $userId2)
                                 ->where('receiver_id', $userId1);
                         });
                     })
                     ->whereNull('deleted_at');
    }

    /**
     * Create a channel message.
     */
    public static function createChannelMessage(array $attributes, User $fromUser): self
    {
        $attributes['sender_id'] = (string) $fromUser->_id;
        $attributes['type'] = 'channel';
        
        return self::create($attributes);
    }

    /**
     * Create a direct message.
     */
    public static function createDirectMessage(array $attributes, User $fromUser): self
    {
        $attributes['sender_id'] = (string) $fromUser->_id;
        $attributes['type'] = 'direct';

        return self::create($attributes);
    }
}
