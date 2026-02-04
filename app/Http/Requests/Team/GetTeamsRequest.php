<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class GetTeamsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'workspace_id' => 'required|string|exists:workspaces,_id',
        ];
    }
}
