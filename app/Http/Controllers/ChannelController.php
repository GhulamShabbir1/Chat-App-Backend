<?php

namespace App\Http\Controllers;

use App\Http\Requests\Channel\AddChannelMemberRequest;
use App\Http\Requests\Channel\GetChannelsRequest;
use App\Http\Requests\Channel\RemoveChannelMemberRequest;
use App\Http\Requests\Channel\StoreChannelRequest;
use App\Http\Requests\Channel\UpdateChannelRequest;
use App\Http\Resources\ChannelResource;
use App\Models\Channel;
use App\Models\Team;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Validator;

class ChannelController extends Controller
{
    public function index(GetChannelsRequest $request)
    {
        $channels = Channel::where('team_id', (string) $request->team_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'channels' => ChannelResource::collection($channels),
            'count' => $channels->count(),
            'team_id' => $request->team_id,
        ]);
    }

    public function store(StoreChannelRequest $request)
    {
        // Verify team exists - handled by Request 'exists' rule.
        // We can trust it.

        $channel = Channel::createForUser($request->validated(), $request->user());

        if (!$channel) {
             return response()->json(['error' => 'Failed to create channel'], 500);
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
            'channel' => new ChannelResource($channel),
        ]);
    }

    public function update(UpdateChannelRequest $request, $id)
    {
        // UpdateChannelRequest validates team_id present.
        
        $channel = Channel::where('_id', $id)
            ->where('team_id', $request->team_id)
            ->first();

        if (!$channel) {
            return response()->json(['error' => 'Channel not found'], 404);
        }

        if ($channel->owner_id != $request->user()->_id) {
            return response()->json(['error' => 'Only channel owner can update'], 403);
        }

        $channel->update($request->validated());

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

        if ($channel->owner_id != $request->user()->_id) {
            return response()->json(['error' => 'Only channel owner can delete'], 403);
        }

        $channel->delete();

        return response()->json([
            'message' => 'Channel deleted successfully',
        ]);
    }

    public function addMember(AddChannelMemberRequest $request, $id)
    {
        $channel = Channel::where('_id', $id)
            ->where('team_id', $request->team_id)
            ->first();

        if (!$channel) {
            return response()->json(['error' => 'Channel not found'], 404);
        }

        if (!$channel->isPrivate()) {
            return response()->json(['error' => 'Only private channels require member management'], 400);
        }

        if (!$channel->addMember($request->user_id)) {
            return response()->json(['error' => 'User is already a member'], 400);
        }

        return response()->json([
            'message' => 'Member added successfully',
            'channel' => new ChannelResource($channel), // Return resource? Original returned model object attached to message?
            // "channel" => $channel. Let's start transforming to Resource everywhere.
        ]);
    }

    public function removeMember(RemoveChannelMemberRequest $request, $id)
    {
        $channel = Channel::where('_id', $id)
            ->where('team_id', $request->team_id)
            ->first();

        if (!$channel) {
            return response()->json(['error' => 'Channel not found'], 404);
        }

        if (!$channel->isPrivate()) {
            return response()->json(['error' => 'Only private channels require member management'], 400);
        }

        $channel->removeMember($request->user_id);

        return response()->json([
            'message' => 'Member removed successfully',
            'channel' => new ChannelResource($channel),
        ]);
    }
}

