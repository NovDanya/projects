<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenreUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $genreId = $this->route('genre') ?? $this->route('id');

        return [
            'name' => 'sometimes|required|string|max:255|unique:genres,name,' . $genreId,
            'description' => 'sometimes|nullable|string|max:500',
        ];
    }
}
