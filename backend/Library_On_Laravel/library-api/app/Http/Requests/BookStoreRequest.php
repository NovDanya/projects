<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'author_id' => 'required|exists:authors,id',
            'published_year' => 'required|integer|min:1000|max:' . date('Y'),
            'genre_id' => 'nullable|exists:genres,id',
        ];
    }
}
