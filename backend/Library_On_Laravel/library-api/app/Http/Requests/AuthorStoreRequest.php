<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthorStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'country' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date|before:today',
            'biography' => 'nullable|string|max:1000',
        ];
    }
}
