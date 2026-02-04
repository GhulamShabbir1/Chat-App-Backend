<?php

namespace App\Http\Requests\File;

use Illuminate\Foundation\Http\FormRequest;

class GetFilesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // No specific filters required by original implementation, but good to have class.
        ];
    }
}
