<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::random(5),
            'author' => fake()->name(),
            'description' => fake()->paragraph(),
            'excerpt' => fake()->paragraph(),
            'price' => 0,
            'nbr_pages' => 120,
            'publish_year' => 2024,
            'is_published' => true,
        ];
    }
}
