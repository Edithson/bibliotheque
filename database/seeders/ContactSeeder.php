<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Contact::firstOrCreate(
            ['email' => 'victor.hugo@example.com'],
            [
                'name' => 'Victor Hugo',
                'subject' => 'author_request',
                'message' => "Bonjour,\n\nJe souhaite proposer mes manuscrits (Les Misérables, Notre-Dame de Paris) pour publication sur votre plateforme numérique.\nDans l'attente de vos instructions pour la transmission des fichiers PDF.\n\nCordialement,\nVictor Hugo",
                'is_read' => false,
            ]
        );

        Contact::firstOrCreate(
            ['email' => 'alexandre.dumas@example.com'],
            [
                'name' => 'Alexandre Dumas',
                'subject' => 'general',
                'message' => "Bonjour Monsieur le Bibliothécaire,\n\nJe félicite l'équipe pour la mise en place de cette splendide bibliothèque en ligne. Les illustrations et le choix des œuvres sont remarquables.\n\nBien à vous,\nAlexandre Dumas",
                'is_read' => true,
            ]
        );
    }
}
