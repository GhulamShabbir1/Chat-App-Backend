<?php

namespace App\Http\Middleware;

use App\Models\Channel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckChannelAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $channelId = $request->route('channel') ?? $request->route('channelId') ?? $request->input('channel_id');

        if (!$channelId) {
            return response()->json(['error' => 'Channel ID is required'], 400);
        }

        $channel = Channel::find($channelId);

        if (!$channel) {
            return response()->json(['error' => 'Channel not found'], 404);
        }

        $user = $request->user();

        if ($channel->isPrivate() && !in_array($user->_id, $channel->member_ids ?? [])) {
            return response()->json(['error' => 'Access denied to this private channel'], 403);
        }

        $request->merge(['channel' => $channel]);

        return $next($request);
    }
}

