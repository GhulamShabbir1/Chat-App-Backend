<?php

namespace App\Http\Controllers;
use App\Http\Requests\Workspace\AddWorkspaceMemberRequest;
use App\Http\Requests\Workspace\RemoveWorkspaceMemberRequest;
use App\Http\Requests\Workspace\StoreWorkspaceRequest;
use App\Http\Requests\Workspace\UpdateWorkspaceRequest;
use App\Http\Resources\WorkspaceResource;
use App\Mail\WorkspaceInvitation;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

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

    public function store(StoreWorkspaceRequest $request)
    {
        $workspace = Workspace::createForUser($request->validated(), $request->user());

        if (!$workspace) {
             return response()->json(['error' => 'Failed to create workspace'], 500);
        }

        Cache::forget('user_workspaces_' . $request->user()->_id);

        return response()->json([
            'message' => 'Workspace created successfully',
            'workspace' => new WorkspaceResource($workspace),
            'workspace_id' => (string) $workspace->_id,
        ], 201);
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

    public function update(UpdateWorkspaceRequest $request, $id)
    {
        $workspace = Workspace::find($id);

        if (!$workspace) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        if ($workspace->owner_id != $request->user()->_id) {
            return response()->json(['error' => 'Only workspace owner can update'], 403);
        }

        $workspace->update($request->validated());

        Cache::forget('user_workspaces_' . $request->user()->_id);

        return response()->json([
            'message' => 'Workspace updated successfully',
            'workspace' => new WorkspaceResource($workspace),
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

        if (!$workspace->addMember($request->user_id)) {
            return response()->json(['error' => 'User is already a member'], 400);
        }

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

        $workspace->removeMember($request->user_id);

        Cache::forget('user_workspaces_' . $request->user()->_id);
        Cache::forget('user_workspaces_' . $request->user_id);

        return response()->json([
            'message' => 'Member removed successfully',
            'workspace' => new WorkspaceResource($workspace),
        ]);
    }
}

