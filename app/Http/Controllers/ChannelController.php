<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChannelResource;
use App\Models\Channel;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChannelController extends Controller
{
    public function index(Request $request)
    {
        $teamId = $request->query('team_id');
        if (!$teamId) {
            return response()->json(['error' => 'team_id is required'], 400);
        }

        $channels = Channel::where('team_id', (string) $teamId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'channels' => $channels,
            'count' => count($channels),
            'team_id' => (string) $teamId,
        ]);
    }

    public function store(Request $request)
    {
        $teamId = $request->team_id;
        if (!$teamId) {
            return response()->json(['error' => 'team_id is required'], 400);
        }

        // Verify team exists
        $team = Team::where('_id', $teamId)->first();

        if (!$team) {
            return response()->json(['error' => 'Team not found', 'team_id' => $request->team_id], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:public,private',
            'team_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $memberIds = $request->type === 'private' ? [(string) $request->user()->_id] : [];

        $channel = Channel::create([
            'name' => $request->name,
            'description' => $request->description,
            'team_id' => (string) $request->team_id,
            'type' => $request->type,
            'owner_id' => (string) $request->user()->_id,
            'member_ids' => $memberIds,
            'settings' => [],
        ]);

        // Refresh the channel to ensure _id is properly populated
        $channel = $channel->fresh();

        // Fallback: if fresh() returns null, query directly
        if (!$channel || !$channel->_id) {
            $channel = Channel::where('team_id', (string) $request->team_id)
                ->where('name', $request->name)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        return response()->json([
            'message' => 'Channel created successfully',
            'channel' => new ChannelResource($channel),
            'channel_id' => (string) $channel->_id,
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $teamId = $request->query('team_id');
        if (!$teamId) {
            return response()->json(['error' => 'team_id is required'], 400);
        }

        $channel = Channel::where('_id', $id)
            ->where('team_id', $teamId)
            ->first();

        if (!$channel) {
            return response()->json(['error' => 'Channel not found'], 404);
        }

        return response()->json([
            'channel' => $channel,
        ]);
    }

    public function update(Request $request, $id)
    {
        $teamId = $request->query('team_id');
        if (!$teamId) {
            return response()->json(['error' => 'team_id is required'], 400);
        }

        $channel = Channel::where('_id', $id)
            ->where('team_id', $teamId)
            ->first();

        if (!$channel) {
            return response()->json(['error' => 'Channel not found'], 404);
        }

        // Only owner can update
        if ($channel->owner_id != $request->user()->_id) {
            return response()->json(['error' => 'Only channel owner can update'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'type' => 'sometimes|in:public,private',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('name')) {
            $channel->name = $request->name;
        }

        if ($request->has('description')) {
            $channel->description = $request->description;
        }

        if ($request->has('type')) {
            $channel->type = $request->type;
        }

        $channel->save();

        return response()->json([
            'message' => 'Channel updated successfully',
            'channel' => new ChannelResource($channel),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $teamId = $request->query('team_id');
        if (!$teamId) {
            return response()->json(['error' => 'team_id is required'], 400);
        }

        $channel = Channel::where('_id', $id)
            ->where('team_id', $teamId)
            ->first();

        if (!$channel) {
            return response()->json(['error' => 'Channel not found'], 404);
        }

        // Only owner can delete
        if ($channel->owner_id != $request->user()->_id) {
            return response()->json(['error' => 'Only channel owner can delete'], 403);
        }

        $channel->delete();

        return response()->json([
            'message' => 'Channel deleted successfully',
        ]);
    }

    public function addMember(Request $request, $id)
    {
        $teamId = $request->query('team_id');
        if (!$teamId) {
            return response()->json(['error' => 'team_id is required'], 400);
        }

        $channel = Channel::where('_id', $id)
            ->where('team_id', $teamId)
            ->first();

        if (!$channel) {
            return response()->json(['error' => 'Channel not found'], 404);
        }

        if (!$channel->isPrivate()) {
            return response()->json(['error' => 'Only private channels require member management'], 400);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $memberIds = $channel->member_ids ?? [];

        if (in_array($request->user_id, $memberIds)) {
            return response()->json(['error' => 'User is already a member'], 400);
        }

        $memberIds[] = $request->user_id;
        $channel->member_ids = $memberIds;
        $channel->save();

        return response()->json([
            'message' => 'Member added successfully',
            'channel' => $channel,
        ]);
    }

    public function removeMember(Request $request, $id)
    {
        $teamId = $request->query('team_id');
        if (!$teamId) {
            return response()->json(['error' => 'team_id is required'], 400);
        }

        $channel = Channel::where('_id', $id)
            ->where('team_id', $teamId)
            ->first();

        if (!$channel) {
            return response()->json(['error' => 'Channel not found'], 404);
        }

        if (!$channel->isPrivate()) {
            return response()->json(['error' => 'Only private channels require member management'], 400);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $memberIds = $channel->member_ids ?? [];
        $memberIds = array_filter($memberIds, fn($id) => $id != $request->user_id);
        $channel->member_ids = array_values($memberIds);
        $channel->save();

        return response()->json([
            'message' => 'Member removed successfully',
            'channel' => new ChannelResource($channel),
        ]);
    }
}

