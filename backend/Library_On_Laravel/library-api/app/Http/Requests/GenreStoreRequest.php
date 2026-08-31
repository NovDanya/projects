<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenreStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:genres,name',
            'description' => 'nullable|string|max:500',
        ];
    }
}
