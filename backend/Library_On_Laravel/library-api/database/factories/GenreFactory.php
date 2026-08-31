<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GenreFactory extends Factory
{
    private $genres = [
        'Novel', 'Fantasy', 'Detective', 'Poetry',
        'Science Fiction', 'Historical', 'Biography',
        'Horror', 'Romance', 'Adventure', 'Drama'
    ];

    public function definition()
    {
        return [
            'name' => $this->faker->randomElement($this->genres),
            'description' => $this->faker->sentence(),
        ];
    }
}
