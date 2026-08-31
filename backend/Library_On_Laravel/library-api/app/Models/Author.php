<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country',
        'birth_date',
        'biography',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
