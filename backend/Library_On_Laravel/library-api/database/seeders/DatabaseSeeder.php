<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Genre;
use App\Models\Book;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Создаем 10 авторов
        Author::factory()->count(10)->create();

        // Создаем жанры
        Genre::factory()->count(8)->create();

        // Создаем 50 книг
        Book::factory()->count(50)->create();
    }
}
