<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'author_id' => Author::factory(),
            'published_year' => $this->faker->numberBetween(1900, date('Y')),
            'genre_id' => Genre::factory(),
        ];
    }
}
