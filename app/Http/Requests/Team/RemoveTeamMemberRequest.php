<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class RemoveTeamMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        // specific authorization logic can be added here, e.g., checking if user is owner
        // for now, we rely on middleware or controller checks, but base request is authorized
        return true;
    }

    public function rules(): array
    {
        return [
            'workspace_id' => 'required|string|exists:workspaces,_id',
            'user_id' => 'required|string|exists:users,_id',
        ];
    }
}
