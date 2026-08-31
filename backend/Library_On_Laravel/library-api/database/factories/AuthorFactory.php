<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AuthorFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'country' => $this->faker->country(),
            'birth_date' => $this->faker->date(),
            'biography' => $this->faker->paragraphs(3, true),
        ];
    }
}
