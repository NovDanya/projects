<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'author_id' => 'sometimes|required|exists:authors,id',
            'published_year' => 'sometimes|required|integer|min:1000|max:' . date('Y'),
            'genre_id' => 'sometimes|nullable|exists:genres,id',
        ];
    }
}
