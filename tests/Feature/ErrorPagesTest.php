<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('non-existent URL renders custom 404 error page with brand text', function () {
    $response = $this->get('/page-ou-livre-introuvable-999');

    $response->assertStatus(404);
    $response->assertSee('La Bibliothèque des Mots');
    $response->assertSee('CODE ERREUR 404');
    $response->assertSee('Ouvrage ou Page Introuvable');
    $response->assertSee('Retourner aux Rayons');
});

test('forbidden admin access renders custom 403 error page with brand text', function () {
    $guestUser = User::factory()->guest()->create();

    $response = $this->actingAs($guestUser)->get('/admin');

    $response->assertStatus(403);
    $response->assertSee('La Bibliothèque des Mots');
    $response->assertSee('CODE ERREUR 403');
    $response->assertSee('Accès Refusé / Zone Réservée');
    $response->assertSee('Retourner aux Rayons');
});
