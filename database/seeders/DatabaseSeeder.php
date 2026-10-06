<?php

namespace Database\Seeders;

use App\Models\Type;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Création des types d'utilisateurs
        $types = ['guest', 'user', 'admin'];
        foreach ($types as $typeName) {
            Type::firstOrCreate(['name' => $typeName]);
        }

        // Utilisateur administrateur par défaut
        $adminType = Type::where('name', 'admin')->first();
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('c@rabine21'),
                'type_id' => $adminType?->id,
            ]
        );

        // Lancement du seeder pour les catégories
        $this->call(CategorySeeder::class);

        // Lancement du seeder pour les livres
        $this->call(BookSeeder::class);

        // Lancement du seeder pour les téléchargements
        $this->call(DownloadSeeder::class);
    }
}
