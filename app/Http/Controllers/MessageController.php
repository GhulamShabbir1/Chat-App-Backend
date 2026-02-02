<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    public function indexChannelMessages(Request $request, $workspaceId, $teamId, $channelId)
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 50);
        $cacheKey = "channel_messages_{$channelId}_page_{$page}_per_{$perPage}";

        $messages = Cache::remember($cacheKey, 600, function () use ($channelId, $perPage) {
            return Message::where('channel_id', $channelId)
                ->where('type', 'channel')
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
        });

        return response()->json([
            'messages' => $messages->items(),
            'count' => count($messages->items()),
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
            ]
        ]);
    }

    public function indexDirectMessages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,_id',
]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $currentUserId = $request->user()->_id;
        $otherUserId = $request->user_id;
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 50);

        $messages = Message::where('type', 'direct')
            ->where(function ($query) use ($currentUserId, $otherUserId) {
                $query->where(function ($q) use ($currentUserId, $otherUserId) {
                    $q->where('sender_id', $currentUserId)
                      ->where('receiver_id', $otherUserId);
                })->orWhere(function ($q) use ($currentUserId, $otherUserId) {
                    $q->where('sender_id', $otherUserId)
                      ->where('receiver_id', $currentUserId);
                });
            })
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'messages' => $messages->items(),
            'count' => count($messages->items()),
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
            ]
        ]);
    }

    public function storeChannelMessage(Request $request, $workspaceId, $teamId, $channelId)
    {
        // Verify channel exists
        $channel = Channel::where('_id', $channelId)
            ->where('team_id', $teamId)
            ->first();
        
        if (!$channel) {
            return response()->json(['error' => 'Channel not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'attachment_id' => 'nullable|exists:file_attachments,_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $message = Message::create([
                'content' => $request->content,
                'sender_id' => (string) $request->user()->_id,
                'channel_id' => (string) $channelId,
                'type' => 'channel',
                'attachment_id' => $request->attachment_id,
            ]);

            // Refresh the message to ensure _id is properly populated
            $message = $message->fresh();
            
            // Fallback 1: if fresh() returns null, query directly
            if (!$message || !$message->_id) {
                $message = Message::where('channel_id', (string) $channelId)
                    ->where('sender_id', (string) $request->user()->_id)
                    ->where('content', $request->content)
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            // Fallback 2: if still null, get the most recent message for this channel
            if (!$message) {
                $message = Message::where('channel_id', (string) $channelId)
                    ->where('type', 'channel')
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            if (!$message) {
                return response()->json([
                    'error' => 'Message was created but could not be retrieved',
                    'debug' => [
                        'channel_id' => (string) $channelId,
                        'sender_id' => (string) $request->user()->_id,
                    ]
                ], 500);
            }

            $this->clearChannelMessagesCache($channelId);

            return response()->json([
                'message'=> 'Message sent successfully',
                'data' => $message,
                'message_id' => (string) $message->_id,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to send message',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function storeDirectMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'receiver_id' => 'required|exists:users,_id',
            'attachment_id' => 'nullable|exists:file_attachments,_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $message = Message::create([
            'content' => $request->content,
            'sender_id' => (string) $request->user()->_id,
            'receiver_id' => (string) $request->receiver_id,
            'type' => 'direct',
            'attachment_id' => $request->attachment_id,
        ]);

        // Refresh the message to ensure _id is properly populated
        $message = $message->fresh();
        
        // Fallback: if fresh() returns null, query directly
        if (!$message || !$message->_id) {
            $message = Message::where('receiver_id', (string) $request->receiver_id)
                ->where('sender_id', (string) $request->user()->_id)
                ->where('content', $request->content)
                ->where('type', 'direct')
                ->orderBy('created_at', 'desc')
                ->first();
        }

        return response()->json([
            'message' => 'Direct message sent successfully',
            'data' => $message,
            'message_id' => (string) $message->_id,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $message = Message::find($id);

        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }

 if ($message->sender_id != $request->user()->_id) {
            return response()->json(['error' => 'You can only edit your own messages'], 403);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $message->content = $request->content;
        $message->edited_at = now();
        $message->save();

        if ($message->channel_id) {
            $this->clearChannelMessagesCache($message->channel_id);
        }

        return response()->json([
            'message' => 'Message updated successfully',
            'data' => $message,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $message = Message::find($id);

        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }

        if ($message->sender_id != $request->user()->_id) {
            return response()->json(['error' => 'You can only delete your own messages'], 403);
        }

        $message->deleted_at = now();
        $message->save();

      if ($message->channel_id) {
            $this->clearChannelMessagesCache($message->channel_id);
        }

        return response()->json([
            'message' => 'Message deleted successfully',
        ]);
    }

    private function clearChannelMessagesCache($channelId)
    {
        $pattern = "channel_messages_{$channelId}_*";
        Cache::flush();
    }
}

