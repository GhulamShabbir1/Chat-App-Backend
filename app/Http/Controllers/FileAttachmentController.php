<?php

namespace App\Http\Controllers;

use App\Http\Requests\File\GetFilesRequest;
use App\Http\Requests\File\UploadFileRequest;
use App\Models\FileAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use MongoDB\Driver\Exception\Exception as MongoException;

class FileAttachmentController extends Controller
{
    public function upload(UploadFileRequest $request)
    {
        try {
            $fileAttachment = FileAttachment::upload($request->file('file'), $request->user());

            if (!$fileAttachment) {
                return response()->json(['error' => 'Failed to upload file'], 500);
            }

            return response()->json([
                'message' => 'File uploaded successfully',
                'file' => $fileAttachment,
                'file_id' => (string) $fileAttachment->_id,
                'path' => $fileAttachment->path,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Upload failed', 'details' => $e->getMessage()], 500);
        }
    }

    public function download($id)
    {
        $fileAttachment = FileAttachment::find($id);

        if (!$fileAttachment) {
            return response()->json(['error' => 'File not found'], 404);
        }

        try {
            if (!$fileAttachment->path) {
                return response()->json(['error' => 'File does not have a valid path'], 404);
            }

            if (!Storage::disk('gridfs')->exists($fileAttachment->path)) {
                 return response()->json([
                    'error' => 'File not found in GridFS',
                    'path' => $fileAttachment->path,
                ], 404);
            }

            return Storage::disk('gridfs')->download($fileAttachment->path, $fileAttachment->original_filename, [
                'Content-Type' => $fileAttachment->mime_type,
            ]);

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

        if ((string) $fileAttachment->uploader_id !== (string) $request->user()->_id) {
            return response()->json(['error' => 'You can only delete your own files'], 403);
        }

        try {
            $fileAttachment->deleteFile();
        } catch (\Exception $e) {
            \Log::warning('Failed to delete file: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'File deleted successfully',
        ]);
    }

    public function index(GetFilesRequest $request)
    {
        $files = FileAttachment::where('uploader_id', $request->user()->_id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'files' => $files, // Ideally Resource, but keeping consistent.
            'count' => $files->count(),
        ]);
    }
}
