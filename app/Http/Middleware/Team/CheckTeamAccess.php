<?php

namespace App\Http\Middleware;

use App\Models\Team;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTeamAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $teamId = $request->route('team') ?? $request->route('teamId') ?? $request->input('team_id');

        if (!$teamId) {
            return response()->json(['error' => 'Team ID is required'], 400);
        }

        $team = Team::find($teamId);

        if (!$team) {
            return response()->json(['error' => 'Team not found'], 404);
        }

        $user = $request->user();

        if (!in_array($user->_id, $team->member_ids ?? [])) {
            return response()->json(['error' => 'Access denied to this team'], 403);
        }

        $request->merge(['team' => $team]);

        return $next($request);
    }
}

