<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\Download;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can access book detail and download history page', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::create(['name' => 'Roman', 'slug' => 'roman']);

    $book = Book::create([
        'title' => 'Livre de Test Historique',
        'slug' => 'livre-de-test-historique',
        'author' => 'Auteur Exemple',
        'category_id' => $category->id,
        'price' => 2000,
        'description' => 'Un grand récit d\'aventure.',
        'excerpt' => 'Dans un pays lointain...',
        'user_id' => $admin->id,
    ]);

    // Create a download entry
    Download::create([
        'book_id' => $book->id,
        'user_id' => $admin->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Mozilla/5.0 TestAgent',
    ]);

    $response = $this->actingAs($admin)->get("/admin/books/{$book->id}");
    $response->assertStatus(200);
    $response->assertSee('Livre de Test Historique');
    $response->assertSee('Auteur Exemple');
    $response->assertSee('Historique des Achats');
    $response->assertSee($admin->email);
    $response->assertSee('127.0.0.1');
});

test('unauthorized user cannot access admin book details page', function () {
    $guestUser = User::factory()->guest()->create();
    $category = Category::create(['name' => 'Roman', 'slug' => 'roman']);

    $book = Book::create([
        'title' => 'Livre Privé Admin',
        'slug' => 'livre-prive-admin',
        'author' => 'Auteur Privé',
        'category_id' => $category->id,
        'price' => 0,
    ]);

    $this->actingAs($guestUser)->get("/admin/books/{$book->id}")->assertStatus(403);
});

test('downloading a book creates a download record visible in history', function () {
    $authorUser = User::factory()->author()->create();
    $category = Category::create(['name' => 'Roman', 'slug' => 'roman']);

    $book = Book::create([
        'title' => 'Livre Gratuit Téléchargeable',
        'slug' => 'livre-gratuit-telechargeable',
        'author' => 'Auteur Gratuit',
        'category_id' => $category->id,
        'price' => 0,
        'is_published' => true,
    ]);

    // Perform download
    $this->actingAs($authorUser)->get("/books/{$book->slug}/download")->assertStatus(200);

    // Verify DB entry
    $download = Download::where('book_id', $book->id)->first();
    expect($download)->not->toBeNull();
    expect($download->user_id)->toBe($authorUser->id);

    // Verify history page displays the record
    $response = $this->actingAs($authorUser)->get("/admin/books/{$book->id}");
    $response->assertStatus(200);
    $response->assertSee($authorUser->name);
    $response->assertSee($authorUser->email);
});

test('public shop detail page loads for published book', function () {
    $category = Category::create(['name' => 'Poésie', 'slug' => 'poesie']);

    $book = Book::create([
        'title' => 'Les Fleurs du Temps',
        'slug' => 'les-fleurs-du-temps',
        'author' => 'Baudelaire',
        'category_id' => $category->id,
        'price' => 0,
        'is_published' => true,
        'description' => 'Recueil de poésie classique.',
    ]);

    $response = $this->get("/books/{$book->slug}");
    $response->assertStatus(200);
    $response->assertSee('Les Fleurs du Temps');
    $response->assertSee('Baudelaire');
    $response->assertSee('Recueil de poésie classique.');
});
