<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Roman', 'slug' => 'roman'],
            ['name' => 'Histoire', 'slug' => 'histoire'],
            ['name' => 'Sciences', 'slug' => 'sciences'],
            ['name' => 'Jeunesse', 'slug' => 'jeunesse'],
            ['name' => 'Voyage', 'slug' => 'voyage'],
            ['name' => 'Essai', 'slug' => 'essai'],
            ['name' => 'Science-fiction', 'slug' => 'science-fiction'],
            ['name' => 'Fantastique', 'slug' => 'fantastique'],
            ['name' => 'Policier', 'slug' => 'policier'],
            ['name' => 'Romance', 'slug' => 'romance'],
            ['name' => 'Horreur', 'slug' => 'horreur'],
            ['name' => 'Aventure', 'slug' => 'aventure'],
            ['name' => 'Biographie', 'slug' => 'biographie'],
            ['name' => 'Poésie', 'slug' => 'poesie'],
            ['name' => 'Théâtre', 'slug' => 'theatre'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
