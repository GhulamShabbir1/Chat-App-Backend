<?php

namespace App\Http\Requests\Channel;

use Illuminate\Foundation\Http\FormRequest;

class AddChannelMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'team_id' => 'required|string|exists:teams,_id',
            'user_id' => 'required|string|exists:users,_id',
        ];
    }
}
