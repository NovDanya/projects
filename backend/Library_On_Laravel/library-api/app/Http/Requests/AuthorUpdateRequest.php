<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthorUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'country' => 'sometimes|nullable|string|max:100',
            'birth_date' => 'sometimes|nullable|date|before:today',
            'biography' => 'sometimes|nullable|string|max:1000',
        ];
    }
}
