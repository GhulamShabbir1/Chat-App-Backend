<?php

namespace App\Http\Controllers;

use App\Models\FileAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use MongoDB\BSON\ObjectId;
use MongoDB\Driver\Exception\Exception as MongoException;

class FileAttachmentController extends Controller
{
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $file = $request->file('file');
            $originalFilename = $file->getClientOriginalName();
            $mimeType = $file->getMimeType() ?? 'application/octet-stream';
            $size = $file->getSize();

            // Validate file size (max 10MB)
            if ($size > 10485760) {
                return response()->json(['error' => 'File size exceeds 10MB limit'], 422);
            }

            // Get MongoDB database connection
            try {
                $database = app('db')->connection('mongodb')->getDatabase();
                $bucket = $database->selectGridFSBucket();
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Failed to connect to GridFS',
                    'details' => $e->getMessage()
                ], 500);
            }

            // Upload to GridFS
            $stream = fopen($file->getRealPath(), 'r');
            if (!$stream) {
                return response()->json([
                    'error' => 'Failed to open file stream',
                ], 500);
            }

            try {
                $gridfsId = $bucket->uploadFromStream($originalFilename, $stream, [
                    'metadata' => [
                        'contentType' => $mimeType,
                        'uploader_id' => (string) $request->user()->_id,
                        'uploaded_at' => now()->toDateTimeString(),
                        'file_size' => $size,
                    ]
                ]);
            } finally {
                fclose($stream);
            }

            // Create file attachment record
            $fileAttachment = FileAttachment::create([
                'filename' => $originalFilename,
                'original_filename' => $originalFilename,
                'mime_type' => $mimeType,
                'size' => $size,
                'gridfs_id' => (string) $gridfsId,
                'uploader_id' => (string) $request->user()->_id,
            ]);

            // Refresh the file attachment to ensure _id is properly populated
            $fileAttachment = $fileAttachment->fresh();
            
            // Fallback 1: if fresh() returns null, query directly
            if (!$fileAttachment || !$fileAttachment->_id) {
                $fileAttachment = FileAttachment::where('uploader_id', (string) $request->user()->_id)
                    ->where('gridfs_id', (string) $gridfsId)
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            // Fallback 2: if still null, get the most recent file
            if (!$fileAttachment) {
                $fileAttachment = FileAttachment::where('uploader_id', (string) $request->user()->_id)
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            if (!$fileAttachment) {
                return response()->json([
                    'error' => 'File was uploaded but could not be recorded',
                    'gridfs_id' => (string) $gridfsId,
                ], 500);
            }

            return response()->json([
                'message' => 'File uploaded successfully',
                'file' => $fileAttachment,
                'file_id' => (string) $fileAttachment->_id,
                'gridfs_id' => (string) $gridfsId,
            ], 201);

        } catch (MongoException $e) {
            return response()->json([
                'error' => 'Failed to upload file to GridFS',
                'details' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unexpected error during file upload',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function download($id)
    {
        $fileAttachment = FileAttachment::find($id);

        if (!$fileAttachment) {
            return response()->json(['error' => 'File not found'], 404);
        }

        try {
            // Validate gridfs_id exists
            if (!$fileAttachment->gridfs_id) {
                return response()->json(['error' => 'File does not have a valid GridFS ID'], 404);
            }

            // Get MongoDB database connection
            try {
                $database = app('db')->connection('mongodb')->getDatabase();
                $bucket = $database->selectGridFSBucket();
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Failed to connect to GridFS',
                    'details' => $e->getMessage()
                ], 500);
            }

            // Download from GridFS
            try {
                $stream = $bucket->openDownloadStream(new ObjectId($fileAttachment->gridfs_id));
                $contents = stream_get_contents($stream);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'File not found in GridFS',
                    'gridfs_id' => $fileAttachment->gridfs_id,
                    'details' => $e->getMessage()
                ], 404);
            }

            return response($contents)
                ->header('Content-Type', $fileAttachment->mime_type)
                ->header('Content-Disposition', 'attachment; filename="' . $fileAttachment->original_filename . '"')
                ->header('Content-Length', strlen($contents));

        } catch (MongoException $e) {
            return response()->json([
                'error' => 'Failed to download file from GridFS',
                'details' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unexpected error during file download',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $fileAttachment = FileAttachment::find($id);

        if (!$fileAttachment) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // Check ownership
        if ((string) $fileAttachment->uploader_id !== (string) $request->user()->_id) {
            return response()->json(['error' => 'You can only delete your own files'], 403);
        }

        try {
            // Validate gridfs_id exists
            if (!$fileAttachment->gridfs_id) {
                // Just delete the record if no GridFS ID
                $fileAttachment->delete();
                return response()->json([
                    'message' => 'File record deleted successfully',
                ]);
            }

            // Get MongoDB database connection
            try {
                $database = app('db')->connection('mongodb')->getDatabase();
                $bucket = $database->selectGridFSBucket();
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Failed to connect to GridFS',
                    'details' => $e->getMessage()
                ], 500);
            }

            // Delete from GridFS
            try {
                $bucket->delete(new ObjectId($fileAttachment->gridfs_id));
            } catch (\Exception $e) {
                // Log the error but still delete the record
                \Log::warning('Failed to delete GridFS file: ' . $e->getMessage());
            }

            // Delete database record
            $fileAttachment->delete();

            return response()->json([
                'message' => 'File deleted successfully',
            ]);

        } catch (MongoException $e) {
            return response()->json([
                'error' => 'Failed to delete file',
                'details' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unexpected error during file deletion',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $files = FileAttachment::where('uploader_id', $request->user()->_id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'files' => $files,
            'count' => count($files),
        ]);
    }
}
