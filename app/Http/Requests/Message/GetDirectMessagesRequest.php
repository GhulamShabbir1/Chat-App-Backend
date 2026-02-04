<?php

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

class GetDirectMessagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|string|exists:users,_id',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
