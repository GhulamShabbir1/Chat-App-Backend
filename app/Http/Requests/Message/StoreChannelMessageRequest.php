<?php

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

class StoreChannelMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'channel_id' => 'required|string|exists:channels,_id',
            'team_id' => 'required|string|exists:teams,_id',
            'content' => 'required|string',
            'attachment_id' => 'nullable|string|exists:file_attachments,_id',
        ];
    }
}
