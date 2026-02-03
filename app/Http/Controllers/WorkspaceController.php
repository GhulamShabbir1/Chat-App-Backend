<?php

namespace App\Http\Controllers;
use App\Http\Requests\Workspace\AddWorkspaceMemberRequest;
use App\Http\Requests\Workspace\RemoveWorkspaceMemberRequest;
use App\Http\Resources\WorkspaceResource;
use App\Mail\WorkspaceInvitation;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class WorkspaceController extends Controller
{
    public function index(Request $request)
    {
        $workspaces = Workspace::orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'workspaces' => WorkspaceResource::collection($workspaces),
            'count' => count($workspaces),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $workspace = Workspace::create([
                'name' => $request->name,
                'description' => $request->description,
                'owner_id' => (string) $request->user()->_id,
                'member_ids' => [],
                'settings' => [],
            ]);

            // Refresh the workspace to ensure _id is properly populated
            $workspace = $workspace->fresh();
            
            // Fallback 1: if fresh() returns null, query directly
            if (!$workspace || !$workspace->_id) {
                $workspace = Workspace::where('owner_id', (string) $request->user()->_id)
                    ->where('name', $request->name)
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            // Fallback 2: if still null, get the most recent workspace for this owner
            if (!$workspace) {
                $workspace = Workspace::where('owner_id', (string) $request->user()->_id)
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            if (!$workspace) {
                return response()->json([
                    'error' => 'Workspace was created but could not be retrieved',
                    'debug' => [
                        'owner_id' => (string) $request->user()->_id,
                        'name' => $request->name,
                    ]
                ], 500);
            }

            Cache::forget('user_workspaces_' . $request->user()->_id);

            return response()->json([
                'message' => 'Workspace created successfully',
                'workspace' => new WorkspaceResource($workspace),
                'workspace_id' => (string) $workspace->_id,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create workspace',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $workspace = Workspace::find($id);

        if (!$workspace) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        return response()->json([
            'workspace' => new WorkspaceResource($workspace),
        ]);
    }

    public function update(Request $request, $id)
    {
        $workspace = Workspace::find($id);

        if (!$workspace) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        if ($workspace->owner_id != $request->user()->_id) {
            return response()->json(['error' => 'Only workspace owner can update'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

      if ($request->has('name')) {
            $workspace->name = $request->name;
        }

        if ($request->has('description')) {
            $workspace->description = $request->description;
        }

        $workspace->save();

        Cache::forget('user_workspaces_' . $request->user()->_id);

        return response()->json([
            'message' => 'Workspace updated successfully',
            'workspace' => $workspace,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $workspace = Workspace::find($id);

        if (!$workspace) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        if ($workspace->owner_id != $request->user()->_id) {
            return response()->json(['error' => 'Only workspace owner can delete'], 403);
        }

        $workspace->delete();

        Cache::forget('user_workspaces_' . $request->user()->_id);

        return response()->json([
            'message' => 'Workspace deleted successfully',
        ]);
    }

    public function addMember(AddWorkspaceMemberRequest $request)
    {
        $workspace = Workspace::find($request->workspace_id);

        if (!$workspace) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        if ($workspace->owner_id != $request->user()->_id) {
            return response()->json(['error' => 'Only workspace owner can add members'], 403);
        }

        $memberIds = $workspace->member_ids ?? [];

        if (in_array($request->user_id, $memberIds)) {
            return response()->json(['error' => 'User is already a member'], 400);
        }

        $memberIds[] = $request->user_id;
        $workspace->member_ids = $memberIds;
        $workspace->save();

        $user = User::find($request->user_id);
        Mail::to($user->email)->send(new WorkspaceInvitation($workspace, $user));

        Cache::forget('user_workspaces_' . $request->user()->_id);
        Cache::forget('user_workspaces_' . $request->user_id);

        return response()->json([
            'message' => 'Member added successfully',
            'workspace' => new WorkspaceResource($workspace),
        ]);
    }

    public function removeMember(RemoveWorkspaceMemberRequest $request)
    {
        $workspace = Workspace::find($request->workspace_id);

        if (!$workspace) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        if ($workspace->owner_id != $request->user()->_id) {
            return response()->json(['error' => 'Only workspace owner can remove members'], 403);
        }

        $memberIds = $workspace->member_ids ?? [];
        $memberIds = array_filter($memberIds, fn($id) => $id != $request->user_id);
        $workspace->member_ids = array_values($memberIds);
        $workspace->save();

        Cache::forget('user_workspaces_' . $request->user()->_id);
        Cache::forget('user_workspaces_' . $request->user_id);

        return response()->json([
            'message' => 'Member removed successfully',
            'workspace' => new WorkspaceResource($workspace),
        ]);
    }
}

