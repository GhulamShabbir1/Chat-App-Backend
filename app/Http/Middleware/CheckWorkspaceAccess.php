<?php

namespace App\Http\Middleware;

use App\Models\Workspace;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckWorkspaceAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $workspaceId = $request->route('workspace') ?? $request->route('workspaceId') ?? $request->input('workspace_id');

        if (!$workspaceId) {
            return response()->json(['error' => 'Workspace ID is required'], 400);
        }

        $workspace = Workspace::find($workspaceId);

        if (!$workspace) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        $user = $request->user();

        if ($workspace->owner_id != $user->_id && !in_array($user->_id, $workspace->member_ids ?? [])) {
            return response()->json(['error' => 'Access denied to this workspace'], 403);
        }

        $request->merge(['workspace' => $workspace]);

        return $next($request);
    }
}

