<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class FileAttachment extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'file_attachments';

    protected $fillable = [
        'filename',
        'original_filename',
        'mime_type',
        'size',
        'gridfs_id',
        'uploader_id',
    ];

    protected $casts = [
        'size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $visible = [
        '_id',
        'filename',
        'original_filename',
        'mime_type',
        'size',
        'gridfs_id',
        'uploader_id',
        'created_at',
        'updated_at',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'attachment_id');
    }
}
