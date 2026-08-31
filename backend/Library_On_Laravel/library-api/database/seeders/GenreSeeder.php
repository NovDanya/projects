<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
        {
        Genre::factory()->count(30)->create();
    }
}
