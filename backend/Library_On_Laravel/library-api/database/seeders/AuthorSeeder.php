<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        Author::factory()->count(30)->create();
    }
}
