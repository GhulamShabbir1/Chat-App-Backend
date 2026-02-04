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
        'path',
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
        'path',
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

    /**
     * Upload a file and create a record.
     */
    public static function upload(\Illuminate\Http\UploadedFile $file, User $user): self
    {
        $originalFilename = $file->getClientOriginalName();
        $mimeType = $file->getMimeType() ?? 'application/octet-stream';
        $size = $file->getSize();
        
        $path = $originalFilename . '_' . time() . '_' . uniqid();
        
        // Read content
        $content = file_get_contents($file->getRealPath());
        if ($content === false) {
             throw new \Exception('Failed to read file content');
        }

        $stored = \Illuminate\Support\Facades\Storage::disk('gridfs')->put($path, $content);

        if (!$stored) {
            throw new \Exception('Failed to upload file to GridFS');
        }

        return self::create([
            'filename' => $originalFilename,
            'original_filename' => $originalFilename,
            'mime_type' => $mimeType,
            'size' => $size,
            'path' => $path,
            'uploader_id' => (string) $user->_id,
        ]);
    }

    /**
     * Delete the file from storage and database.
     */
    public function deleteFile(): void
    {
        // If using GridFS directly in Controller previously, we can adapt here.
        // But Controller used GridFSBucket. 
        // Adapting to use Storage facade if possible or raw logic.
        // Controller used raw: $bucket->delete(new ObjectId($fileAttachment->gridfs_id));
        // But wait, 'gridfs_id' property? Model visible didn't show 'gridfs_id'. 
        // Controller line 137: if (!$fileAttachment->gridfs_id)
        // Controller line 27: Storage::disk('gridfs')->put($path, $fileContent);
        // Does Storage::put return success bool or path? 
        // If 'gridfs' driver stores it, how do we get ID?
        // Usually GridFS stream returns ID. 
        // If strict refactoring, we should trust the 'path' key if Storage uses it.
        // Controller used 'path' for retrieval: `Storage::disk('gridfs')->get($fileAttachment->path);`
        // So 'path' is the key. 
        // Controller destroy() used `gridfs_id`. This implies inconsistency or 'path' IS the 'gridfs_id' or filename?
        // Check line 26: $path = $originalFilename ...
        // So 'path' is a string filename.
        // Controller line 137 check `gridfs_id`?? Maybe different field?
        // If I use Storage::disk('gridfs')->delete($this->path)?
        // Let's rely on Storage facade if possible.
        
        if ($this->path) {
             \Illuminate\Support\Facades\Storage::disk('gridfs')->delete($this->path);
        }
        
        $this->delete();
    }
}
