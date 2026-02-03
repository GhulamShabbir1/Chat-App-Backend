<?php

namespace App\Http\Controllers;

use App\Http\Requests\Team\StoreTeamRequest;
use App\Http\Resources\TeamResource;
use App\Mail\TeamInvitation;
use App\Models\Team;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $workspaceId = $request->query('workspace_id');
        if (!$workspaceId) {
            return response()->json(['error' => 'workspace_id is required'], 400);
        }

        $teams = Team::where('workspace_id', (string) $workspaceId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'teams' => $teams,
            'count' => count($teams),
            'workspace_id' => (string) $workspaceId,
        ]);
    }

    public function store(StoreTeamRequest $request)
    {
        // Ensure workspace exists
        $workspace = Workspace::where('_id', $request->workspace_id)->first();
        if (!$workspace) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        try {
            $teamData = [
                'name' => $request->name,
                'description' => $request->description,
                'workspace_id' => (string) $request->workspace_id,
                'owner_id' => (string) $request->user()->_id,
                'member_ids' => [(string) $request->user()->_id],
                'settings' => [],
            ];

            $team = Team::create($teamData);

            // Refresh the team to ensure _id is properly populated
            $team = $team->fresh();

            // Fallback 1: if fresh() returns null, query directly
            if (!$team || !$team->_id) {
                $team = Team::where('workspace_id', (string) $request->workspace_id)
                    ->where('owner_id', (string) $request->user()->_id)
                    ->where('name', $request->name)
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            // Fallback 2: if still null, query by just workspace and name
            if (!$team) {
                $team = Team::where('workspace_id', (string) $request->workspace_id)
                    ->where('name', $request->name)
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            // Fallback 3: if still null, get the most recent team
            if (!$team) {
                $team = Team::where('workspace_id', (string) $request->workspace_id)
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            if (!$team) {
                return response()->json([
                    'error' => 'Team was created but could not be retrieved',
                    'debug' => [
                        'workspace_id' => (string) $request->workspace_id,
                        'name' => $request->name,
                        'owner_id' => (string) $request->user()->_id,
                    ]
                ], 500);
            }

            return response()->json([
                'message' => 'Team created successfully',
                'team' => new TeamResource($team),
                'team_id' => (string) $team->_id,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create team',
                'message' => $e->getMessage(),
                'debug' => [
                    'workspace_id' => (string) $request->workspace_id,
                    'name' => $request->name,
                ]
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $workspaceId = $request->query('workspace_id');
        if (!$workspaceId) {
            return response()->json(['error' => 'workspace_id is required'], 400);
        }

        $team = Team::where('_id', $id)
            ->where('workspace_id', $workspaceId)
            ->first();

        if (!$team) {
            return response()->json(['error' => 'Team not found'], 404);
        }

        return response()->json([
            'team' => $team,
        ]);
    }

    public function update(Request $request, $id)
    {
        $workspaceId = $request->query('workspace_id');
        if (!$workspaceId) {
            return response()->json(['error' => 'workspace_id is required'], 400);
        }

        $team = Team::where('_id', $id)
            ->where('workspace_id', $workspaceId)
            ->first();

        if (!$team) {
            return response()->json(['error' => 'Team not found'], 404);
        }

        // Only owner can update
        if ($team->owner_id != $request->user()->_id) {
            return response()->json(['error' => 'Only team owner can update'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('name')) {
            $team->name = $request->name;
        }

        if ($request->has('description')) {
            $team->description = $request->description;
        }

        $team->save();

        return response()->json([
            'message' => 'Team updated successfully',
            'team' => $team,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $workspaceId = $request->query('workspace_id');
        if (!$workspaceId) {
            return response()->json(['error' => 'workspace_id is required'], 400);
        }

        $team = Team::where('_id', $id)
            ->where('workspace_id', $workspaceId)
            ->first();

        if (!$team) {
            return response()->json(['error' => 'Team not found'], 404);
        }

        // Only owner can delete
        if ($team->owner_id != $request->user()->_id) {
            return response()->json(['error' => 'Only team owner can delete'], 403);
        }

        $team->delete();

        return response()->json([
            'message' => 'Team deleted successfully',
        ]);
    }

    public function addMember(Request $request, $id)
    {
        $workspaceId = $request->query('workspace_id');
        if (!$workspaceId) {
            return response()->json(['error' => 'workspace_id is required'], 400);
        }

        $team = Team::where('_id', $id)
            ->where('workspace_id', $workspaceId)
            ->first();

        if (!$team) {
            return response()->json(['error' => 'Team not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $memberIds = $team->member_ids ?? [];

        if (in_array($request->user_id, $memberIds)) {
            return response()->json(['error' => 'User is already a member'], 400);
        }

        $memberIds[] = $request->user_id;
        $team->member_ids = $memberIds;
        $team->save();

        $user = User::find($request->user_id);
        Mail::to($user->email)->send(new TeamInvitation($team, $user));

        return response()->json([
            'message' => 'Member added successfully',
            'team' => $team,
        ]);
    }

    public function removeMember(Request $request, $id)
    {
        $workspaceId = $request->query('workspace_id');
        if (!$workspaceId) {
            return response()->json(['error' => 'workspace_id is required'], 400);
        }

        $team = Team::where('_id', $id)
            ->where('workspace_id', $workspaceId)
            ->first();

        if (!$team) {
            return response()->json(['error' => 'Team not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $memberIds = $team->member_ids ?? [];
        $memberIds = array_filter($memberIds, fn($id) => $id != $request->user_id);
        $team->member_ids = array_values($memberIds);
        $team->save();

        return response()->json([
            'message' => 'Member removed successfully',
            'team' => $team,
        ]);
    }
}

